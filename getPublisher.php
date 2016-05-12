<?php

function dump( $data )
{
	echo "<pre>";
	var_dump( $data );
	echo "<br/>";
}

 include 'simple_html_dom.php';
$html = file_get_contents('publisher.txt'); 

echo "<pre>";
#$arr = explode(" ", $html);
preg_match_all("/([\x80-\xff])+/", $html, $arr );

foreach( $arr[0] as $key => $value )
{
	$pubArr[] = substr($arr[0][$key] , 3);

}


$publisherArr = array_unique( $pubArr );


mysql_connect('localhost', 'root', '');
mysql_select_db('library');
mysql_query('set names utff8');



foreach( $publisherArr as $key => $value )
{
	$publisherName = $publisherArr[$key];
	$sql = "INSERT lib_publisher(publisherName) VALUES('{$publisherName}')";
	$query = mysql_query( $sql );
	var_dump( $query );
	echo "<br/>";
}

















?>
