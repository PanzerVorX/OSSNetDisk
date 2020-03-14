<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		
		body{
			background-color: #272822;
		}

		textarea{
			font-family: 微软雅黑;
			font-size: 15px;
			font-weight: bold;
			margin: auto;
			width: 98%;
			height: 472px;
			resize: none;
			outline: 8px solid #26A3DB;
		}

		h1{
			font-size: 25px;
			color: #26A3DB;
		}
		
		input[type='button']{
			font-size: 25px;
			margin-left:45%;
			margin-top:45px;
		}
	</style>
</head>
<?php
	if(isset($_GET['query'])){
		$noticeId=$_GET['noticeId'];
		$bucket=$_GET['bucket'];
		$parentPath=$_GET['parentPath'];
		
		$tableName=$bucket."_notice";//表名
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		$pdo->exec('set names utf8');
		
		//$sql="update {$tableName} set notice_status='已读' where notice_id={$noticeId}";
		//$pdo->exec($sql);
		$sql="select * from {$tableName} where notice_id='{$noticeId}'";
		$smt=$pdo->query($sql);
		$rows=$smt->fetchAll();
		$noticeContent=$rows[0]['notice_content'];
		echo "<center><textarea name='text' readonly>{$noticeContent}</textarea></center>";
	}
?>
<input type="button" name="back" value="返回" onclick=location='noticeList.php?query=ok&bucket=<?php echo $bucket;?>&parentPath=<?php echo $parentPath;?>'>