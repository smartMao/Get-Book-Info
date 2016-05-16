<script type="text/javascript" src="app/js/jquery.js"></script>
<?php

header('charset=utf8');

$connent = mysql_connect('localhost', 'root' , '');
mysql_select_db('library');
mysql_query('set names gbk');


$bookTypeSql = "SELECT * FROM lib_bookType";
$bookTypeQuery = mysql_query( $bookTypeSql );


$bookshelfSql = "SELECT * FROM lib_bookshelf";
$bookshelfQuery = mysql_query( $bookshelfSql );


$publisherSql = "SELECT * FROM lib_publisher";
$publisherQuery = mysql_query( $publisherSql );

?>
 
<!-- form表单 begin  -->

<span>urlCode说明：是当当图书网分类页的url中的编号，它被用于区分出不同的图书类型，<br/>那么在此你需要输入的就是 http://category.dangdang.com/<span style='color:red'>cp01.24.02.00.00.00</span>.html中 红色的部分,<br/>程序会自动翻页把整个分类 100页内的 6000 本书完成数据抓取</span>
<br/>	
<span> 页码说明：用于指定爬取的图书分类从第几页开始，默认是第一页</span>


<form method="post" action="handle.php">

<select name='bookType' id='bookType' >
<?php
while( $bookTypeRows = mysql_fetch_array( $bookTypeQuery, MYSQL_ASSOC ) )
{
?>
	<option value="<?php echo $bookTypeRows['PK_bookTypeID']; ?>" ><?php echo $bookTypeRows['bookTypeName']; ?></option>
<?php
}
?>

</select>

<select name='bookshelf' id='bookshelf'>
<?php
while( $bookshelfRows = mysql_fetch_array( $bookshelfQuery, MYSQL_ASSOC ) )
{
?>
	<option value="<?php echo $bookshelfRows['PK_bookshelfID']; ?>" ><?php echo $bookshelfRows['bookshelfName']; ?></option>
<?php
}
?>

</select>

	<input type='text' name='page' placeholder="页码" >
	<input type='text' name='urlCode'  placeholder="urlCode"/>
	<input type='submit' />
</form>


<script type='text/javascript'>
	window.onload = function(){

		// 当下拉框被选择的时候，使用 session 存储选中时的 value ，好让下次的进入页面就已经直接选中

		bookType = $('#bookType');	
		bookType.change(function(){
			sessionStorage.bookTypeVal = $(this).val();
		});


		bookshelf = $('#bookshelf');
		bookshelf.change(function(){
			sessionStorage.bookshelfVal = $(this).val();
		});

		
		// 读取 session 的值，对比 option 的值，一样就选中

		bookTypeOptions = $('#bookType option');
		bookTypeOptions.each(function(){
			if( $(this).val() == sessionStorage.bookTypeVal ){
				$(this).attr('selected','selected');		
			}
		});	


		bookshelfOptions = $('#bookshelf option');
		bookshelfOptions.each(function(){
			if( $(this).val() == sessionStorage.bookshelfVal ){
				$(this).attr('selected','selected');		
			}
		});

	}
	

</script>


