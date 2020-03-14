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

		input{
			margin: 10px;
			width: 80px;
			height: 30px;
			font-size: 18px;
			font-weight: bold;
			margin-right:50px;
		}
	</style>
</head>
<form>
	<center><textarea name='noticeContent'></textarea></center>
	<input type='hidden' name='bucket' value=<?php  echo $_GET['bucket']; ?>>
	<input type='hidden' name='parentPath' value=<?php  echo $_GET['parentPath']; ?>>
	<center><input type='submit' name='send'><input type='button'  value='返回' name='back' onclick=location='noticeList.php?query=ok&bucket=<?php  echo $_GET['bucket']; ?>&parentPath=<?php  echo $_GET['parentPath']; ?>'></center>
</form>
<?php
	if(isset($_GET['send'])){
		$bucket=$_GET['bucket'];
		$parentPath=$_GET['parentPath'];
		$noticeContent=$_GET['noticeContent'];
		$tableName=$bucket."_notice";//表名
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		$pdo->exec('set names utf8');
		date_default_timezone_set('PRC');
		$now_time=date('Y-n-j H:i:s');
		$sql="insert into {$tableName} (notice_content,notice_date,user_name) values ('{$noticeContent}','{$now_time}','{$bucket}');";
		$pdo->exec($sql);
		echo "<script>location='noticeList.php?query=ok&bucket={$bucket}&parentPath={$parentPath}'</script>";
	}
?>