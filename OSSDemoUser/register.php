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
		
		select{
			font-size: 25px;
		}
		

	</style>
</head>
<body>
	<form method="post">
		<table>
			<tr><th colspan="2"><h1>OSS客户端</h1></th></tr>
			<tr><td class='td1'>用户名：</td><td><input type="text" name="userName"></td></tr>
			<tr><td>密码：</td><td><input type="password" name="userPwd"></td></td></tr>
			<tr><td>存储地区：</td>
			<td>
			<select name="location">
						<option value="oss-cn-shanghai.aliyuncs.com">华东</option>
						<option value="oss-cn-shenzhen.aliyuncs.com">华南</option>
						<option value="oss-cn-chengdu.aliyuncs.com">华西</option>
						<option selected value="oss-cn-beijing.aliyuncs.com">华北</option>
					</select>
			</td>
			</tr>
			<tr><td><input type="submit" name="ok" value="注册"></td><td><a href="login.php"><input type="button"  value="返回" ></a></td></tr>
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
		$location=$_POST['location'];
		$accessKeyId = $_POST['AccessKeyId'];
		$accessKeySecret = $_POST["AccessKeySecret"];
			
		if($userName && $userPwd){
			
			$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
			$sql='set names utf8';
			$pdo->exec($sql);
			
			$pdo->beginTransaction();//开启事务机制 
			
			$sql="select * from user_msg where user_name=? ";
			$smt=$pdo->prepare($sql);
			$smt->bindParam(1,$userName);
			$smt->execute();
			$rows=$smt->fetchAll();
			if(!$rows){
				$sql="insert into user_msg values (?,?,?);";
				$smt=$pdo->prepare($sql);
				$smt->bindParam(1,$userName);
				$smt->bindParam(2,$userPwd);
				$smt->bindParam(3,$location);
				if($smt->execute()){
					try{
						$ossClient = new OssClient($accessKeyId, $accessKeySecret, $location);
						if(!$ossClient->doesBucketExist($userName)){
							$options = array(
								OssClient::OSS_STORAGE => OssClient::OSS_STORAGE_IA
							);
							//设置存储空间的权限为公共读，默认是私有读写
							$ossClient->createBucket($userName, OssClient::OSS_ACL_TYPE_PUBLIC_READ, $options);
							$pdo->commit();
							echo "<script>alert('注册成功');location='login.php'</script>";
						}	
					}
					catch(OssException $e){
						echo "<script>alert('注册失败，存储空间名已存在');location='login.php'</script>";
					}
				}
				else{
					echo "<script>alert('注册失败');location='login.php'</script>";
				}
			}
			else{
				echo "<script>alert('用户名已存在');</script>";
			}
		}
		else{
			echo "<script>alert('用户名与密码不能为空');</script>";
		}
	}
?>
