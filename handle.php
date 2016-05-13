<?php
/*
 * update:
 *
 *	改进思路，将这个小爬虫做得更加的智能。
 *	
 *		运行此程序（handle.php) 需要从 index.php POST提交三个参数过
 *		来， 分别是 图书类型、书架、分类页的url编号（最重要）（简称为
 *		url编号 ）， 这个 url编号大概是这样子：cp01.24.02.00.00.00，
 *		它是从 http://category.dangdang.com/pg2-cp01.24.02.00.00.00.html
 *		所提取出来的，在当当中这种分类最多有 100页，每页有 60 本书，
 *		根据循环，把每页 60 本书的 url 都循环出来（其实是把书的 
 *		id=1234567 这样的id属性循环出来，然后拼装到一个 Url中，就形成了
 *		一本图书信息页面 的 url。有了这个 url 后，使用 simple_html_dom
 *		（是一个开源的 php 爬虫类库） 里面的 file_get_html( $url ),
 *		获取到一个 html dom 结构，从中读取图书信息，存入数据库。
 *		这就完成了一次循环。 而这样的循环估计要做 6000 次（如果要把一个
 *		分类下的所有图书都循环完成的话） 
 *
 *		缺点：没有！！哈哈哈哈
 * 
 *
 */


mysql_connect('localhost','root', '');
mysql_select_db('library');
mysql_query('set names gbk');

header("Content-type:text/html;charset=gbk");

include_once 'simple_html_dom.php';


$bookTypeID  = $_POST['bookType'];
$bookshelfID = $_POST['bookshelf']; 
$urlCode     = $_POST['urlCode'];
#$bookTypeID  = 13; 
#$bookshelfID = 56; 
date_default_timezone_set('PRC');
$date = date('Y-m-d');


function dump( $data )
{
	echo "<pre>";
	var_dump( $data );
	echo "<br/>";
}





function getPage( $html, $isSelfSupport )
{
	if( $isSelfSupport ){

		$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 1);
		$strPage = $liData->innertext;
		preg_match('/[0-9]+/', $strPage, $page);

		if( empty( $page ) ){
			$page[] = 0;		
		}
		return $page[0];

	} else {
		// 因为所有的 非当当自营的图书的页码都是为 0 , 那我就不获取了。
		return '0';	
	}


}


function getPrice( $html , $isSelfSupport )
{
	if( $isSelfSupport ){

		$price = $html -> find('.price_d', 0)->innertext;
		$price  = substr( $price , 5);
		return $price;

	} else {
		$price = $html -> find('#salePriceTag' ,  0)->innertext;
		return $price;
	}
}


function getAuthorAndTranslator( $html , $isSelfSupport)
{
	if( $isSelfSupport ){
		// 当当自营
		//
		// 绕过有些图书是没有作者的！ , 因为作者　包含在.messbox_info的<div>
		// 如果它里面的元素个素少于４个的话，说明没有作者信息
		$elm = $html->find('.messbox_info');
		$elmCount = count( $elm[0] -> children );
		
		if( $elmCount >= 4 ){

			$linkCount  = count( $html-> find('#author', 0)->find('a') );
			$data['author']     = $html -> find('#author', 0) -> find('a', 0)->innertext;	
			$data['translator'] = $html -> find('#author', 0) -> find('a', $linkCount -1)->innertext;	

			if( $linkCount == 1) $data['translator'] = null;
		
		} else {
			// 没有作者信息可以获取,只好自己赋值　
			$data['author'] = '未知';	
			$data['translator'] = null;
		}

		return $data;

	} else {
		// 非当当自营
		$data['author'] = $html->find('.book_messbox', 0 )->find('div', 0) -> find('.show_info_right', 0) -> find('a', 0)->innertext;
		$data['translator'] = null;

		return $data;
	}
}




function getISBN( $html , $isSelfSupport )
{
	if( $isSelfSupport ){

		#$div = $html->find('.pro_content', 0);
		$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 9);
		$strISBN = $liData->innertext;
		preg_match('/[0-9]{8,13}/', $strISBN, $ISBN);

		return $ISBN[0];

	} else {

		$ISBN = $html->find('.book_messbox', 0 )->find('div', 11)->innertext;
		return $ISBN;
	}

}




function getBookName( $html, $isSelfSupport )
{

	if ( $isSelfSupport ){
		$title = $html -> find('.name_info', 0) -> find('h1', 0) -> title;
	} else {
		$title = $html -> find('.head', 0) -> find('h1', 0) -> innertext;
	}

	preg_match("/([a-zA-Z0-9+]|[\x80-\xff]|\s)+/", $title, $bookName);                                         
	$bookName = $bookName[0];
	return $bookName;
}



function getPublisher( $html, $isSelfSupport )
{
	if( $isSelfSupport ){

		$elm = $html->find('.messbox_info');
		$elmCount = count( $elm[0] -> children );
		
		if( $elmCount >= 4 ){
			$publisher = $html -> find('.messbox_info', 0) -> find('span', 1)->find('a', 0)->innertext;
		} else {
			// 没有出版社信息可以获取,只好自己赋值　
			$publisher = '未知';
		}

		return $publisher;

	} else {
		$publisher = $html->find('.book_messbox', 0 )->find('a', 1)->innertext;
		return $publisher;
	}
}








// 本类型的页数，正常是有 100页（除去第一页的 url 不能用之外）剩下99页，（1页有60本书的Url, 99页有 5940 本）！！！                                 
$pageCount = 17;  
for( $i=1; $i < $pageCount; $i++){
	// 一个类型内的 99 页循环

    $categoryHtml = file_get_html('http://category.dangdang.com/pg'. $i .'-'. $urlCode .'.html');
    
    $perPageCount = 60;

    for( $j=0; $j < $perPageCount; $j++) {
        $id = $categoryHtml -> find('#component_0__0__3058', 0 ) -> find('li', $j )->id;

        // 是否是当当自营，为什么要判断这个，因为进入到一本书的商品页面，当当自营的 与 非当当自营的 页面布局是有区别（包括 id class 名不一等等）
        // 因此，获图书信息之前，先判断下 $isSelfSupport 再进行信息的获取.
       	$shop = $categoryHtml -> find('#component_0__0__3058', 0 ) -> find('li', $j )->find('div', 0)->find('p', 6)->class;
       	if( $shop == 'dang'){
       		$isSelfSupport = true;
       	} elseif( $shop == 'link'){
       		$isSelfSupport = false;
       	}



        $bookUrl = 'http://product.dangdang.com/'. $id .'.html';
		echo $bookUrl;
		echo "<br/>";

		$html = file_get_html( $bookUrl  );   

		# wait ......

		$arr = getAuthorAndTranslator( $html, $isSelfSupport );
		 
		$bookName   = getBookName( $html, $isSelfSupport );
		$ISBN       = getISBN( $html , $isSelfSupport ); // 做到这里
		$author     = $arr['author'];
		$translator = $arr['translator'];
		$price      = getPrice( $html , $isSelfSupport );
		$page       = getPage( $html , $isSelfSupport);
		$publisher  = getPublisher( $html , $isSelfSupport);



		# 循环出 lib_publisher 里的全部的出版社信息

		$publisherSql  = "SELECT * FROM lib_publisher";
		$publisherQuery = mysql_query( $publisherSql );
		while ( $rows = mysql_fetch_assoc( $publisherQuery ))
		{
			$publisherArr[] = $rows;
		}


		foreach ( $publisherArr as $pubKey => $pubVal )
		{
			// 如果爬取的图书信息 的 出版社 和 lib_publisher 有相匹配的. 
			if( $publisher == $publisherArr[$pubKey]['publisherName'] ){
					// 出版社ID
					$publisherID = $publisherArr[$pubKey]['PK_publisherID'];		
			}
		}


		// 将爬取到的 图书信息插入到 lib_bookInfo 后得到新插入条目的 ID

		$bookInfoSql = "INSERT lib_bookInfo(bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bookInfoBookPage ) VALUES ( '$bookName', '$ISBN', '$author', '$translator', '$price', '$page');";

		$bookInfoQuery = mysql_query( $bookInfoSql);
		$bookInfoID    = mysql_insert_id();

		if( !$bookInfoQuery ){
			echo "bookInfo 数据表插入错误";
			echo "<br/>";
		}


		
		# 得到上面的 bookInfo ID ，在加上 提交的 图书类型 和 书架 , 和 自动匹配的 出版社名称 

		$bookRelationSql = "INSERT lib_bookRelationship( FK_bookInfoID, FK_publisherID, FK_bookTypeID, FK_managerID, FK_bookshelfID, bookRelationshipStorageTime ) VALUES( '$bookInfoID', '$publisherID', '$bookTypeID', '1', '$bookshelfID', '$date');";
		$bookRelationQuery = mysql_query( $bookRelationSql );

		if( $bookRelationQuery ){
			echo "数据已成功进入数据库! <a href='index.php'> goback </a> ";
			echo "<br/>";
		} else {
			echo "数据插入 bookRelationship 时失败";
		}





    } // 每个页面内的 60 本书的 url 循环
} // 进入图书列表的页面 的循环 用于把 99 个页面 都循环出来 


