<?php
	require "aliyun-oss-php-sdk-2.3.0/autoload.php";
	use OSS\OssClient;
	use OSS\Core\OssException;

	//反序列化
	$handle=fopen("./serialize.txt","r+");
	$serialize=fread($handle,filesize("./serialize.txt"));
	fclose($handle);
	$ossClient=unserialize($serialize);	

	function deleteFile($ossClient,$bucket,$parentPath){

		$prefix = $parentPath;
		$delimiter = '';
		$nextMarker = '';
		$maxkeys = 1000;
		$options = array(
		    'delimiter' => $delimiter,
		    'prefix' => $prefix,
		    'max-keys' => $maxkeys,
		    'marker' => $nextMarker,
		);

		try {
		    $listObjectInfo = $ossClient->listObjects($bucket, $options);
		    $objectList = $listObjectInfo->getObjectList();
		    if (!empty($objectList)) {
		    	$dirArr=array();
			    foreach ($objectList as $objectInfo) {
			    	$object=$objectInfo->getKey();
			    	$objectMeta = $ossClient->getObjectMeta($bucket, $object);//获取文件元信息

			    	//获取文件路径除去父目录之后的部分
			    	if($parentPath){
			    		$tempStr=substr($object,strlen($parentPath));
			    	}
			    	else{
			    		$tempStr=$object;
			    	}

			    	$isDir=strpos($tempStr,'/');//判断是否是文件
			    	if(!$isDir){
			    		if($tempStr){
			    			$ossClient->deleteObject($bucket,$object);
			    		}
			    	}
			    	else{
			    		$dirName=substr($tempStr,0,strpos($tempStr,'/')+1);
			    		if(!in_array($dirName,$dirArr)){
			    			$dirArr[]=$dirName;
			    			deleteFile($ossClient,$bucket,$object);
			    		}
			    	}
			    }
			}
			if($parentPath){
				$ossClient->deleteObject($bucket,$parentPath);
			}
		} 
		catch (Exception $e) {//catch中调递归————只许成功不许失败
			deleteFile($ossClient,$bucket,$parentPath);
		}
	}

	if(isset($_POST['delete'])){
		$bucketArr=$_POST['bucket'];
		foreach ($bucketArr as $key => $value) {
			try{
				deleteFile($ossClient,$value,'');
				$ossClient->deleteBucket($value);
				$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
				$sql='set names utf8';
				$pdo->exec($sql);
				$sql="delete from user_msg where user_name=?";
				$smt=$pdo->prepare($sql);
				$smt->bindParam(1,$value);
				$smt->execute();
				
				//删除站内通告表
				$tableName=$value."_notice";//表名
				$result = $pdo->query("show tables like '". $tableName."'");
				$row = $result->fetchAll();
				if(count($row)){//若存在则删除
					$sql="drop table {$tableName}";
					$pdo->exec($sql);
				} 
				
				//删除用户文件查询表
				$tableName=$value."_user";//表名
				$result = $pdo->query("show tables like '". $tableName."'");
				$row = $result->fetchAll();
				if(count($row)){//若存在则删除
					$sql="drop table {$tableName}";
					$pdo->exec($sql);
				} 
				
				//删除管理员文件查询表
				$tableName=$value."_admin";//表名
				$result = $pdo->query("show tables like '". $tableName."'");
				$row = $result->fetchAll();
				if(count($row)){//若存在则删除
					$sql="drop table {$tableName}";
					$pdo->exec($sql);
				}
				
				
			}
			catch(Exception $e){
				echo "<script>alert('删除存储空间失败');location='frameOperation.php'</script>";
			}
		}
		echo "<script>alert('删除存储空间成功');location='frameOperation.php'</script>";
	}
	else{
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>

		span{
			font-size:35px;
			font-weight: bold;
			margin-left: 10px;
		}

		form{
			margin-top: 10%;
			font-size: 30px;
			font-weight: bold;
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
			margin-left: 40%;
			width: 200px;
			margin-top: 50px;
		}

		input[type='button']{
			margin-left: 40%;
			width: 200px;
			margin-top: 50px;
		}
		
	</style>
</head>
<body>
	<form method="post">
		<table>
		<tr><td><span>存储空间名：</span></style></td><td><input type="text" name="bucketName"></td></tr>
		<tr><td><input type="submit" name="createBucket" value="创建"></td><td><input type="button" name="back" value="取消" onclick=location='frameOperation.php'></td></tr>
		</table>
	</form>
</body>
</html>
<?php 
	}
?>
<?php

	if(isset($_POST["createBucket"])){
		$bucket=$_POST['bucketName'];
		$location=$_COOKIE['location']?$_COOKIE['location']:'oss-cn-beijing.aliyuncs.com';
		try {	    

		    // 设置存储空间的存储类型为低频访问类型，默认是标准类型
		    $options = array(
		        OssClient::OSS_STORAGE => OssClient::OSS_STORAGE_IA
		    );

		    //设置存储空间的权限为公共读，默认是私有读写
		    $ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ, $options);
			
			$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
			$pdo->beginTransaction();//开启事务机制 
			$sql='set names utf8';
			$pdo->exec($sql);
			
			$sql="insert into user_msg values (?,?,?);";
			$smt=$pdo->prepare($sql);
			$smt->bindParam(1,$bucket);
			$smt->bindParam(2,$bucket);
			$smt->bindParam(3,$location);
			if($smt->execute()){
				
			//创建站内通告表
			$tableName=$bucket."_notice";//表名
			$result = $pdo->query("show tables like '". $tableName."'");
			$row = $result->fetchAll();
			if(count($row)){//若存在则清空表
				$sql="truncate {$tableName}";
			} 
			else {//若不存在则创建表
				$sql="create table {$tableName}(
							notice_id int auto_increment primary key,
							notice_content LONGTEXT not null,
							notice_date char(50) not null,
							notice_status char(10) not null default '未读',
							user_name char(20) not null
						)engine=InnoDB default charset=gbk;";
			}
			$pdo->exec($sql);
				
			date_default_timezone_set('PRC');
			$now_time=date('Y-n-j H:i:s');
			$sql="insert into {$tableName} (notice_content,notice_date,user_name) values ('用户：{$bucket}，欢迎开通并使用在线网盘','{$now_time}','{$bucket}');";
			$pdo->exec($sql);			
			
			$pdo->commit();
				echo "<script>alert('创建成功，用户名与密码都为存储空间名');location='storeRecords.php?query=ok&bucket={$bucket}&parentPath='</script>";
			};
		} 
		catch (OssException $e) {
		    echo "<script>alert('创建失败');location='frameOperation.php'</script>";
		}
	}
?>