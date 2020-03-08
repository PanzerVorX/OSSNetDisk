<?php
			$userName=$_GET['bucket'];
			
			$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
			$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
			$pdo->exec('set names utf8');
			$sql="select * from user_msg where user_name ='{$userName}'";
			$smt=$pdo->query($sql);
			$rows=$smt->fetchAll();
			$row=$rows[0];
			
			$userPwd=$row['user_pwd'];
			$location=$row['location'];
			$userLocation='';
			if(strcmp($location,'oss-cn-shanghai.aliyuncs.com')==0){
				$userLocation = '华东' ;
			}
			elseif(strcmp($location,'oss-cn-shenzhen.aliyuncs.com')==0){
				$userLocation = '华南' ;
			}
			elseif(strcmp($location,'oss-cn-chengdu.aliyuncs.com')==0){
				$userLocation = '华西' ;
			}
			elseif(strcmp($location,'oss-cn-beijing.aliyuncs.com')==0){
				$userLocation = '华北' ;
			}
?>
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
		
		td{
			font-size: 30px;
		}
		

	</style>
</head>
<body>
	<form method="get">
		<table>
			<tr><th colspan="2"><h1>用户信息</h1></th></tr>
			<tr><td class='td1'>用户名：</td><td><input type="text" name="userName" value=<?php echo $userName;?> readonly></td></tr>
			<tr><td>密码：</td><td><input type="text" name="userPwd" value=<?php echo $userPwd;?>></td></tr>
			<tr><td>存储地区：</td><td><input type="text" name="userLocation"  value=<?php echo $userLocation;?>  readonly></td></tr>
			<!--<tr><td>Endpoint：</td><td><input type="text" name="Endpoint"></td></tr>-->
			<tr><td><input type="submit" name="ok" value="修改"></td><td><input type="button" name="back" value="返回"  onclick=location='showBucketFile.php?query=ok&bucket=<?php echo $_GET['bucket']; ?>&parentPath=<?php echo @$_GET["parentPath"]; ?>'></td></tr>
			<input type="hidden" name="bucket" value=<?php echo $_GET['bucket'];?>>
			<input type="hidden" name="parentPath" value=<?php echo @$_GET["parentPath"];?>>
		</table>
	</form>
</body>
</html>
<?php
	if(isset($_GET['ok'])){
		$userName=$_GET['bucket'];
		$userPwd=$_GET['userPwd'];
		$parentPath=$_GET["parentPath"];
		
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		$pdo->exec('set names utf8');
		
		if($userPwd){
			$sql="update user_msg set user_pwd=? where  user_name=?";
			$smt=$pdo->prepare($sql);
			$smt->bindValue(1,$userPwd);
			$smt->bindValue(2,$userName);
			if($smt->execute()){
				echo "<script>alert('修改成功');location='showBucketFile.php?query=ok&bucket={$userName}&parentPath={$parentPath}'</script>";
			}
			else{
				echo "<script>alert('修改失败');''</script>";
			}
		}
		else{
			echo "<script>alert('密码不能为空');''</script>";
		}
	}
?>