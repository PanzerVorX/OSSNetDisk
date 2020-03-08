<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">

		body{
			background-color: #272822;
			color: #26A3DB;
		}

		form{
			margin-top: 6%;
			font-size: 30px;
			font-weight: bold;
		}
		

		h1{
			font-size: 50px;
			font-weight: bold;
			margin-bottom: 80px;
		}

		table{
			margin: auto;
		}

		input{
			height: 35px;
			width: 380px;
			font-size: 20px;
			font-weight: bold;
		}

		input[type='submit']{
			width: 200px;
			margin-top: 50px;
		}
		
		input[type='button']{
			width: 200px;
			margin-top: 50px;
			margin-left: 200px;
		}
		

	</style>
</head>
<body>
	<form method="post">
		<table>
			<tr><th colspan="2"><h1>OSS客户端</h1></th></tr>
			<tr><td class='td1'>用户名：</td><td><input type="text" name="userName"></td></tr>
			<tr><td>密码：</td><td><input type="password" name="userPwd"></td></td></tr>
			<!--<tr><td>Endpoint：</td><td><input type="text" name="Endpoint"></td></tr>-->
			<tr><td><input type="submit" name="ok" value="登录"></td><td><a href="register.php"><input type="button" name="register" value="注册" ></a></td></tr>
			<tr><td><input type='hidden' name='AccessKeyId' value='LTAIKj75FiRzS9HH'></td><td><input type='hidden' name='AccessKeySecret' value='re83ecbPPkWPeVBu8rghlJru9mr3K5'></td></tr>
			
		</table>
	</form>
</body>
</html>

<?php
	require "aliyun-oss-php-sdk-2.3.0/autoload.php";
	use OSS\OssClient;
	use OSS\Core\OssException;
	if(isset($_POST['ok'])){
		
		$userName=$_POST['userName'];
		$userPwd=$_POST['userPwd'];
		
		//查询账户数据库
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$sql='set names utf8';
		$pdo->exec($sql);
		
		$sql="select * from user_msg where user_name='$userName' and user_pwd='$userPwd'";
		$smt=$pdo->query($sql);
		$rows=$smt->fetchAll();
		if($rows){
				//echo "<script>alert('登录成功');</script>";
				$row=$rows[0];
				$endpoint=$row['location'];
				$accessKeyId = $_POST['AccessKeyId'];
				$accessKeySecret = $_POST["AccessKeySecret"];
				try {
				//创建OSS操作对象
				$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
				//多页面都需的操作类对象可序列化为共用文件以减少传递构造参数
				//序列化OSS操作对象并存入本地文件
				$serialize=serialize($ossClient);
				$handle=fopen('serialize.txt','w+');
				fwrite($handle,$serialize);
				fclose($handle);
				session_start();
				setcookie("bucket",$userName,time()+60*60*24*7);
				
				echo "<script>alert('登录成功');location='main.php'</script>";
			} 
			catch (OssException $e) {
				echo "<script>alert('账号信息错误1');</script>";
			}
		}
		else{
			echo "<script>alert('账户信息错误');</script>";
		}
	}
?>