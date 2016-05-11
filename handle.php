
<?php
function test()
{
mysql_connect('localhost','root', '');
mysql_select_db('library');
mysql_query('set names utf8');
	#$sql = 'INSERT lib_bookInfo(bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bo    okInfoBookPage ) VALUES ( "新智元机器", "9787121281778", "杨静", "isss", "46.60", "889");';
	$sql = 'INSERT lib_publisher(publisherName) VALUES("小猫出版社");';
	$query = mysql_query( $sql );

	var_dump( $query );
}
test();
/*
include_once 'simple_html_dom.php';


$html = file_get_html( $_POST['url']);

echo "<pre>";

# 获取图书页码
function getPage( $html )
{

	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 1);
	$strPage = $liData->innertext;
	preg_match('/[0-9]+/', $strPage, $page);
	return $page[0];

}


# 获取图书的单价
function getPrice( $html )
{
	$price = $html -> find('.price_d', 0)->innertext;
	$price  = substr( $price , 5);
	return $price;

}


# 获取图书的作者　和　译者
function getAuthorAndTranslator( $html )
{

	$linkCount  = count( $html-> find('#author', 0)->find('a') );

	$data['author']     = $html -> find('#author', 0) -> find('a', 0)->innertext;	
	$data['translator'] = $html -> find('#author', 0) -> find('a', $linkCount -1)->innertext;	

	// 如果 <a> 列表长度只有１，说明只有一个作者名字，是中国的.
	if( $linkCount == 1) $data['translator'] = null;

	return $data;
}



# 获取图书 ISBN 号
function getISBN( $html )
{
	#$div = $html->find('.pro_content', 0);
	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 9);
	$strISBN = $liData->innertext;
	preg_match('/[0-9]{13}/', $strISBN, $ISBN);
	return $ISBN[0];
}



# 获取图书名称
function getBookName( $html )
{
	$title = $html -> find('.name_info', 0) -> find('h1', 0) -> title;
	preg_match("/([a-zA-Z]|[\x80-\xff])+/", $title, $bookName);                                         
	$bookName = $bookName[0];
	return $bookName;
}

function returnData()
{
	$arr = getAuthorAndTranslator( $html );
	 
	$bookName   = getBookName( $html );
	$ISBN       = getISBN( $html );
	$author     = $arr['author'];
	$translator = $arr['translator'];
	$price      = getPrice( $html );
	$page       = getPage( $html );

	return $bookName;
}

#echo  $bookName;
#echo "<br/>";
#echo  $ISBN;
#echo "<br/>";
#echo  $author;
#echo "<br/>";
#echo  $translator;
#echo "<br/>";
#echo  $price;
#echo "<br/>";
#echo  $page;
#echo "<br/>";




if ( empty( $bookName)){
	echo "书名问题导致失败";exit;
}

if ( empty( $ISBN)){
	echo "ISBN号问题导致失败";exit;
}

if ( empty( $author)){
	echo "作者名问题导致失败";exit;
}

if ( empty( $price)){
	echo "单价问题导致失败";exit;
}



# 数据库连接


#$bookInfoSql = "INSERT INTO lib_bookInfo (bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bookInfoBookPage ) VALUES ( '$bookName', '$ISBN', '$author', '$translator', '$price', '$page');";

#$bookInfoSql = 'INSERT lib_bookInfo(bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bookInfoBookPage ) VALUES ( "新智元机器", "9787121281778", "杨静", "asdas", "46.40", "380");';

#$bookInfoQuery = mysql_query( $bookInfoSql);


#var_dump( $connect );
#var_dump( $bookInfoQuery );
#var_dump( $bookInfoSql);


*/
