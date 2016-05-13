<?php
/*
 * update:
 *	改进思路，将这个小爬虫做得更加的智能。
 *
 *	1. 将图书的 Url, 存入一个单独的文件里，以回车分割Url, 假如此文件命名为 computer.txt
 *	2. 在php 中使用读取文件的函数将 computer.txt 文件内的url全读取出来（或许之后还需要正则匹配下)
 *	3. 在程序中循环使用这些url, 通过 file_get_html( $url ) 将每个 url 的图书信息都读取出来
 *	4. 如果在读取图书信息过程中出现问题（如: 无页码, ISBN号是旧版) 的话，那就不要了，把此次循环跳过。
 *
 * 操作流程：
 *	1. 选择 图书类型 和 书架 下拉框 （存进 sessionStorage ) [完成]
 *  2. 而 出版社 呢，我改为在程序中自动匹配。（预先在 lib_publisher 插入 500 多个出版社的名字) [完成]
 *	   然后，等读取到图书的出版社名字时，直接跟数据库中的 出版社 进行对比.
 *  3. 输入框呢 就直接输入 computer.txt 这个存有许多 url 的文件。
 *  4. 利用正则把　computer.txt 文件中的 url 单独取出来. 
 * 
 *
 */
mysql_connect('localhost','root', '');
mysql_select_db('library');
mysql_query('set names gbk');

header("Content-type:text/html;charset=gbk");

include_once 'simple_html_dom.php';


#$bookTypeID  = $_POST['bookType'];
#$bookshelfID = $_POST['bookshelf']; 
$bookTypeID  = 13; 
$bookshelfID = 56; 
date_default_timezone_set('PRC');
$date = date('Y-m-d');


function dump( $data )
{
	echo "<pre>";
	var_dump( $data );
	echo "<br/>";
}





function getPage( $html )
{

	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 1);
	$strPage = $liData->innertext;
	preg_match('/[0-9]+/', $strPage, $page);
	if( empty( $page ) ){
		$page[] = 0;		
	}
	return $page[0];

}


function getPrice( $html )
{
	$price = $html -> find('.price_d', 0)->innertext;
	$price  = substr( $price , 5);
	return $price;

}


function getAuthorAndTranslator( $html )
{
	$linkCount  = count( $html-> find('#author', 0)->find('a') );

	$data['author']     = $html -> find('#author', 0) -> find('a', 0)->innertext;	
	$data['translator'] = $html -> find('#author', 0) -> find('a', $linkCount -1)->innertext;	


	if( $linkCount == 1) $data['translator'] = null;

	return $data;
}




function getISBN( $html )
{
	#$div = $html->find('.pro_content', 0);
	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 9);
	$strISBN = $liData->innertext;
	preg_match('/[0-9]{8,13}/', $strISBN, $ISBN);

	return $ISBN[0];
}




function getBookName( $html )
{
	$title = $html -> find('.name_info', 0) -> find('h1', 0) -> title;
	preg_match("/([a-zA-Z0-9+]|[\x80-\xff]|\s)+/", $title, $bookName);                                         
	$bookName = $bookName[0];
	return $bookName;
}



function getPublisher( $html )
{
	$publisher = $html -> find('.messbox_info', 0) -> find('span', 1)->find('a', 0)->innertext;
	return $publisher;
}








// 本类型的页数，正常是有 100页（除去第一页的 url 不能用之外）剩下99页，（1页有60本书的Url, 99页有 5940 本）！！！                                 
$pageCount = 4;  
for( $i=2; $i < $pageCount; $i++){
	// 一个类型内的 99 页循环

    $categoryHtml = file_get_html('http://category.dangdang.com/pg'. $i .'-cp01.63.00.00.00.00.html');
    
    $perPageCount = 60;

    for( $j=0; $j < $perPageCount; $j++) {
        $id = $categoryHtml -> find('#component_0__0__3058', 0 ) -> find('li', $j )->id;
        $bookUrl = 'http://product.dangdang.com/'. $id .'.html';

		$html = file_get_html( $bookUrl  );   

		# wait ......

		$arr = getAuthorAndTranslator( $html );
		 
		$bookName   = getBookName( $html );
		$ISBN       = getISBN( $html );
		$author     = $arr['author'];
		$translator = $arr['translator'];
		$price      = getPrice( $html );
		$page       = getPage( $html );
		$publisher  = getPublisher( $html );



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


