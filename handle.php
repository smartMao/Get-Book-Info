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

function dump( $data )
{
	echo "<pre>";
	var_dump( $data );
	echo "<br/>";
}


include_once 'simple_html_dom.php';



header("Content-type:text/html;charset=gbk");


function getPage( $html )
{

	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 1);
	$strPage = $liData->innertext;
	preg_match('/[0-9]+/', $strPage, $page);
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
	preg_match('/[0-9]{13}/', $strISBN, $ISBN);
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





// 读取文件信息

$file      = file_get_contents('computer.txt');
$fileNotBr = preg_replace('/\r|\n/', '`', $file);
$urlList   = explode('`', $fileNotBr);



foreach ( $urlList as $key => $value )
{
	if( strlen( $urlList[$key]) < 20 ){ continue; }
	$html = file_get_html( $value  );	

	# wait ......

	$arr = getAuthorAndTranslator( $html );
	 
	$book[$key]['bookName']   = getBookName( $html );
	$book[$key]['ISBN']       = getISBN( $html );
	$book[$key]['author']     = $arr['author'];
	$book[$key]['translator'] = $arr['translator'];
	$book[$key]['price']      = getPrice( $html );
	$book[$key]['page']       = getPage( $html );
	$book[$key]['publisher']  = getPublisher( $html );
	 	
}

dump( $book );exit;
// 出版社名称已准备好


/*
 *  问题检测
 */
/*
if ( empty( $bookName)){
	echo "书名问题";exit;
}

if ( empty( $ISBN)){
	echo "ISBN问题";exit;
}

if ( empty( $author)){
	#
}

if ( empty( $price)){
	#
}
 */



mysql_connect('localhost','root', '');
mysql_select_db('library');
mysql_query('set names gbk');


$publisherSql  = "SELECT * FROM lib_publisher";
$publisherQuery = mysql_query( $publisherSql );
while ( $rows = mysql_fetch_assoc( $publisherQuery ))
{
	$publisherArr[ ] = $rows;
}
dump( $publisherArr );exit;





$bookInfoSql = "INSERT lib_bookInfo(bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bookInfoBookPage ) VALUES ( '$bookName', '$ISBN', '$author', '$translator', '$price', '$page');";

$bookInfoQuery = mysql_query( $bookInfoSql);
$bookInfoID    = mysql_insert_id();

if( !$bookInfoQuery ){
   	echo "bookInfo 数据表插入错误";exit;
}

$bookTypeID  = $_POST['bookType'];
$bookshelfID = $_POST['bookshelf'];

date_default_timezone_set('PRC');
$date = date('Y-m-d');

# bookRelationship 在此
$bookRelationSql = "INSERT lib_bookRelationship( FK_bookInfoID, FK_publisherID, FK_bookTypeID, FK_managerID, FK_bookshelfID, bookRelationshipStorageTime ) VALUES( '$bookInfoID', '$publisherID', '$bookTypeID', '1', '$bookshelfID', '$date');";
$bookRelationQuery = mysql_query( $bookRelationSql );

if( $bookRelationQuery ){
	echo "insert data sussess! click ! <a href='index.php'> goback </a> ";
} else {
	echo "数据插入 bookRelationship 时失败";
}






