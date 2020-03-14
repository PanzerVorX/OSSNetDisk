<?php 
	if(isset($_GET['delete'])){
		$noticeId=$_GET['noticeId'];
		$bucket=$_GET['bucket'];
		$parentPath=$_GET['parentPath'];
		
		$tableName=$bucket."_notice";//表名
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		$pdo->exec('set names utf8');
		
		$sql="delete from {$tableName} where notice_id={$noticeId}";
		$pdo->exec($sql);
		echo "<script>location='noticeList.php?query=ok&bucket={$bucket}&parentPath={$parentPath}'</script>";
	}
?>