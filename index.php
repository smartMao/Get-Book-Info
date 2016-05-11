<?php

header('charset=utf8');

$connent = mysql_connect('localhost', 'root' , '');
mysql_select_db('library');
mysql_query('set names utf8');


$bookTypeSql = "SELECT * FROM lib_bookType";
$bookTypeQuery = mysql_query( $bookTypeSql );


$bookshelfSql = "SELECT * FROM lib_bookshelf";
$bookshelfQuery = mysql_query( $bookshelfSql );


$publisherSql = "SELECT * FROM lib_publisher";
$publisherQuery = mysql_query( $publisherSql );

?>

<!-- form±íµ¥ begin  -->

<form method="post" action="poc.php">

<select name='bookType'>
<?php
while( $bookTypeRows = mysql_fetch_array( $bookTypeQuery, MYSQL_ASSOC ) )
{
?>
	<option value="<?php echo $bookTypeRows['PK_bookTypeID']; ?>" ><?php echo $bookTypeRows['bookTypeName']; ?></option>
<?php
}
?>

</select>

<select name='bookshelf'>
<?php
while( $bookshelfRows = mysql_fetch_array( $bookshelfQuery, MYSQL_ASSOC ) )
{
?>
	<option value="<?php echo $bookshelfRows['PK_bookshelfID']; ?>" ><?php echo $bookshelfRows['bookshelfName']; ?></option>
<?php
}
?>

</select>

<select name='publisher'>
<?php
while( $publisherRows = mysql_fetch_array( $publisherQuery, MYSQL_ASSOC ) )
{
?>
	<option value="<?php echo $publisherRows['PK_publisherID']; ?>" ><?php echo $publisherRows['publisherName']; ?></option>
<?php
}
?>

</select>

	
	<input type='text' name='url' />
	<input type='submit' />
</form>



