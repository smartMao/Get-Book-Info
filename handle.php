
<?php
function test()
{
mysql_connect('localhost','root', '');
mysql_select_db('library');
mysql_query('set names utf8');
	#$sql = 'INSERT lib_bookInfo(bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bo    okInfoBookPage ) VALUES ( "����Ԫ����", "9787121281778", "�", "isss", "46.60", "889");';
	$sql = 'INSERT lib_publisher(publisherName) VALUES("Сè������");';
	$query = mysql_query( $sql );

	var_dump( $query );
}
test();
/*
include_once 'simple_html_dom.php';


$html = file_get_html( $_POST['url']);

echo "<pre>";

# ��ȡͼ��ҳ��
function getPage( $html )
{

	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 1);
	$strPage = $liData->innertext;
	preg_match('/[0-9]+/', $strPage, $page);
	return $page[0];

}


# ��ȡͼ��ĵ���
function getPrice( $html )
{
	$price = $html -> find('.price_d', 0)->innertext;
	$price  = substr( $price , 5);
	return $price;

}


# ��ȡͼ������ߡ��͡�����
function getAuthorAndTranslator( $html )
{

	$linkCount  = count( $html-> find('#author', 0)->find('a') );

	$data['author']     = $html -> find('#author', 0) -> find('a', 0)->innertext;	
	$data['translator'] = $html -> find('#author', 0) -> find('a', $linkCount -1)->innertext;	

	// ��� <a> �б���ֻ�У���˵��ֻ��һ���������֣����й���.
	if( $linkCount == 1) $data['translator'] = null;

	return $data;
}



# ��ȡͼ�� ISBN ��
function getISBN( $html )
{
	#$div = $html->find('.pro_content', 0);
	$liData = $html->find('.pro_content', 0) -> find('ul', 0) -> find('li', 9);
	$strISBN = $liData->innertext;
	preg_match('/[0-9]{13}/', $strISBN, $ISBN);
	return $ISBN[0];
}



# ��ȡͼ������
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
	echo "�������⵼��ʧ��";exit;
}

if ( empty( $ISBN)){
	echo "ISBN�����⵼��ʧ��";exit;
}

if ( empty( $author)){
	echo "���������⵼��ʧ��";exit;
}

if ( empty( $price)){
	echo "�������⵼��ʧ��";exit;
}



# ���ݿ�����


#$bookInfoSql = "INSERT INTO lib_bookInfo (bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bookInfoBookPage ) VALUES ( '$bookName', '$ISBN', '$author', '$translator', '$price', '$page');";

#$bookInfoSql = 'INSERT lib_bookInfo(bookInfoBookName, bookInfoBookISBN, bookInfoBookAuthor, bookInfoBookTranslator, bookInfoBookPrice, bookInfoBookPage ) VALUES ( "����Ԫ����", "9787121281778", "�", "asdas", "46.40", "380");';

#$bookInfoQuery = mysql_query( $bookInfoSql);


#var_dump( $connect );
#var_dump( $bookInfoQuery );
#var_dump( $bookInfoSql);


*/
