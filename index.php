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
 
<!-- form�� begin  -->

<span>urlCode˵�����ǵ���ͼ��������ҳ��url�еı�ţ������������ֳ���ͬ��ͼ�����ͣ�<br/>��ô�ڴ�����Ҫ����ľ��� http://category.dangdang.com/<span style='color:red'>cp01.24.02.00.00.00</span>.html�� ��ɫ�Ĳ���,<br/>������Զ���ҳ���������� 100ҳ�ڵ� 6000 �����������ץȡ</span>
<br/>	
<span> ҳ��˵��������ָ����ȡ��ͼ�����ӵڼ�ҳ��ʼ��Ĭ���ǵ�һҳ</span>


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

	<input type='text' name='page' placeholder="ҳ��" >
	<input type='text' name='urlCode'  placeholder="urlCode"/>
	<input type='submit' />
</form>


<script type='text/javascript'>
	window.onload = function(){

		// ��������ѡ���ʱ��ʹ�� session �洢ѡ��ʱ�� value �������´εĽ���ҳ����Ѿ�ֱ��ѡ��

		bookType = $('#bookType');	
		bookType.change(function(){
			sessionStorage.bookTypeVal = $(this).val();
		});


		bookshelf = $('#bookshelf');
		bookshelf.change(function(){
			sessionStorage.bookshelfVal = $(this).val();
		});

		
		// ��ȡ session ��ֵ���Ա� option ��ֵ��һ����ѡ��

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


