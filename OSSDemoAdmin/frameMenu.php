<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<style type="text/css">
		
		body{
			background-color:#6495ED;
		}

		span{
			font-size:35px;
			font-weight: bold;
			margin-left: 10px;
		}

		a{
			text-decoration: none;
			color: #000;
		}

		a:hover{
			text-decoration: underline;
			color: #8470FF;
		}

		input[type='checkbox']{
			width: 20px;
			height: 20px;
		}

		.splitLine{
			background-color: #888;
			height: 5px;
		}
		
		input[type='submit']{
			margin-left: 8px;
			border-radius: 10px;
			background-color: #7FFFD4;
		}

		input[type='button']{
			margin-left: 8px;
			border-radius: 10px;
			background-color: #7FFFD4;
		}
		
		select{
			margin-left:10px;
			font-size:15px;
		}
		
		.div1{
			position: relative;
		}
		
		.div2{
			position: absolute;
			top:0px;
			left:260px;
		}

	</style>
</body>
</html>
<?php
	require "aliyun-oss-php-sdk-2.3.0/autoload.php";
	use OSS\OssClient;
	use OSS\Core\OssException;
	
	//首次进入默认查看华北地区存储空间
	$location=@$_POST['location']?$_POST['location']:'oss-cn-beijing.aliyuncs.com';
	
	//之后创建存储空间所需的地区参数
	session_start();
	setcookie("location",$location,time()+60*60*24*7);
	
	//创建OSS操作对象所需参数
	
	$accessKeyId=@$_SESSION["accessKeyId"];
	$accessKeySecret=@$_SESSION["accessKeySecret"];
	if($accessKeyId && $accessKeySecret){
		
	//$accessKeySecret=$_COOKIE['accessKeySecret'];
	//$accessKeyId=$_COOKIE['accessKeyId'];
	
	if(@$_POST['location']){
			$ossClient = new OssClient($accessKeyId, $accessKeySecret, $location);
			$serialize=serialize($ossClient);
			$handle=fopen('serialize.txt','w+');
			fwrite($handle,$serialize);
			fclose($handle);
	}
	
	//反序列化
	$handle=fopen("./serialize.txt","r+");
	$serialize=fread($handle,filesize("./serialize.txt"));
	fclose($handle);
	$ossClient=unserialize($serialize);

	try{
		$bucketListInfo = $ossClient->listBuckets();
	}
	catch(OssException $e){
		echo "<script>alert('列举存储空间报错');</script>";
	}

	echo "<div class='div1'>";
	echo "<form name='fm1' method='post' action='operationBucket.php' target='frameOperation'>";
	echo "<span>存储空间<sapn>";
	//表单内被选中的提交按钮(submit)在提交参数时屏蔽其它提交按钮元素（不会传递其它提交按钮的参数）
	echo "<input type='submit' name='create' value='创建'>";
	echo "<input type='submit' name='delete' value='删除'>";
	echo "<div class='splitLine'></div>";
	$bucketList = $bucketListInfo->getBucketList();
	foreach($bucketList as $bucket) {
	     $bucketName=$bucket->getName();
		 try{
			$region=$ossClient->getBucketLocation($bucketName);
			if(strncmp($location,$region,strlen($region))===0){//判断是否为对应地区的用户
				echo "<input type='checkbox' name='bucket[]' value={$bucketName}><a href='storeRecords.php?query=ok&bucket={$bucketName}&parentPath=' target='frameOperation'>{$bucketName}</a><br>";
			}
		 }
		 catch(OssException $e){
			 
		 }
	}
	echo "</form>";
	
	echo "<div class='div2'>";
	echo "<form name='fm2' method='post' >";
	echo "<input type='submit' name='refresh' value='刷新' onclick=location=''>";
	echo '<select name="location">';
	if(strpos($location,'oss-cn-shanghai.aliyuncs.com')===0)
		echo '<option selected value="oss-cn-shanghai.aliyuncs.com">华东</option>';
	else
		echo '<option value="oss-cn-shanghai.aliyuncs.com">华东</option>';
	if(strpos($location,'oss-cn-shenzhen.aliyuncs.com')===0)
		echo '<option selected value="oss-cn-shenzhen.aliyuncs.com">华南</option>';
	else
		echo '<option value="oss-cn-shenzhen.aliyuncs.com">华南</option>';
	if(strpos($location,'oss-cn-chengdu.aliyuncs.com')===0)
		echo '<option selected value="oss-cn-chengdu.aliyuncs.com">华西</option>';
	else
		echo '<option value="oss-cn-chengdu.aliyuncs.com">华西</option>';
	if(strpos($location,'oss-cn-beijing.aliyuncs.com')===0)
		echo '<option selected value="oss-cn-beijing.aliyuncs.com">华北</option>';
	else
		echo '<option value="oss-cn-beijing.aliyuncs.com">华北</option>';
	echo '</select>';
	echo "</form>";
	echo "</div>";
	echo "</div>";
	}
	else{
		echo "<script>alert('管理员帐号缓存失效，请重新登录');</script>";
	}
	
?>