<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">

		body{
			background-color: #272822;
			color: #26A3DB;
		}
		
		h1{
			font-size: 50px;
		}

		table{
			margin: auto;
			font-size: 30px;
			font-weight: bold;
		}
		
		table td{
			border: 2px solid #00f;
			text-align: center;
			width: 250px;
			height: 30px;
			font-size: 16px;
			font-weight: bold;
		}
		
		a{
			text-decoration: none;
			color: #05558a;// #572084;
		}

		a:hover{
			text-decoration: underline;
			color:#e3e74b; // #458B00;
		}
		
		div{
			height:400px;
			weight:600px;
			overflow-y:scroll;
			overflow-x：hidden;
		}
		
		button{
			font-size: 25px;
			margin-left:48%;
			margin-top:45px;
		}
		
		img{
			width: 26px;
			height: 24px;
			margin-right: 8px;
		}
		
	</style>
</head>
 <?php
	if(isset($_GET['query'])){
		$bucket=$_GET['bucket'];
		$parentPath=$_GET['parentPath'];

		echo "<div>";
		echo "<table>";
		echo '<tr><th colspan="4"><h1>站内通告</h1></th></tr>';
		$tableName=$bucket."_notice";//表名
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		$pdo->exec('set names utf8');
		$sql="select * from {$tableName};";
		$smt=$pdo->query($sql);
		$rows=$smt->fetchAll();
		if($rows){
			echo "<tr><td>消息</td><td>接收时间</td><td>状态</td><td colspan='2'>操作</td></tr>";
			foreach ($rows as $row) {
				$noticeId=$row['notice_id'];
				$noticeContent=$row['notice_content'];
				$noticeDate=$row['notice_date'];
				$noticeStatus=$row['notice_status'];
				if(strlen($noticeContent)>16){
					$noticeContent=mb_substr($noticeContent,0,6)."......";
				}
				echo "<tr><td>{$noticeContent}</td><td>{$noticeDate}</td><td>{$noticeStatus}</td><td colspan='2'><a href='noticeContent.php?query=ok&bucket={$bucket}&parentPath={$parentPath}&noticeId={$noticeId}'>查看</a></td></tr>";
			}
		}
		echo "</table>";
		echo "</div>";
		echo "<a href='showBucketFile.php?query=ok&bucket={$bucket}&parentPath={$parentPath}'><button>返回</button></a>";
	}
 ?>