 <?php
	require "aliyun-oss-php-sdk-2.3.0/autoload.php";
	use OSS\OssClient;
	use OSS\Core\OssException;
	
	function reNameFile($ossClient,$bucket,$parentPath,$saveFilePrefix){

		if(strrpos($parentPath,'/')==(strlen($parentPath)-1)){//判断是否为目录
			
			$ossClient->uploadFile($bucket,$saveFilePrefix."/",'serialize.txt');//创建目录

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
								
								 $ossClient->copyObject($bucket, $object, $bucket, $saveFilePrefix."/".$tempStr);
								 $ossClient->deleteObject($bucket, $object);
				    		}
				    	}
				    	else{
				    		$dirName=substr($tempStr,0,strpos($tempStr,'/'));
				    		if(!in_array($dirName,$dirArr)){
				    			$dirArr[]=$dirName;
				    			 reNameFile($ossClient,$bucket,$object,$saveFilePrefix."/".$dirName);
				    		}
				    	}
				    }
				}
				$ossClient->deleteObject($bucket, $parentPath);
			} 
			catch (Exception $e) {
				var_dump($e);
			}
		}
		else{
			$ossClient->copyObject($bucket, $parentPath, $bucket, $saveFilePrefix);
			$ossClient->deleteObject($bucket, $parentPath);
		}
	}
	
	$bucket=$_GET['bucket'];
	$filePath=$_GET['filePath'];
	$parentPath=$_GET['parentPath'];

	if($parentPath){
		$tempStr=substr($filePath,strlen($parentPath));
	}
	else{
		$tempStr=$filePath;
	}
	
	//去除目录后的/
	if(strrpos($tempStr,'/')==strlen($tempStr)-1){
		$tempStr=substr($tempStr,0,strlen($tempStr)-1);
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
		

	</style>
</head>
<body>
	<form method="get">
		<table>
			<tr><th colspan="2"><h1>重命名</h1></th></tr>
			<tr><td class='td1'>原文件名：</td><td><input type="text" name="tempStr" readonly value=<?php echo $tempStr;?>></td></tr>
			<tr><td>设置文件：</td><td><input type="text" name="setName"></td></td></tr>
			<tr><td><input type="submit" name="ok" value="修改"></td><td><a href="showBucketFile.php?query=ok&bucket=<?php  echo $_GET['bucket'];?>&parentPath=<?php  echo $_GET['parentPath'];?>"><input type="button" name="back" value="返回" ></a></td></tr>
			<input type='hidden' name='bucket' value=<?php  echo $_GET['bucket'];?>>
			<input type='hidden' name='filePath' value=<?php echo $_GET['filePath'];?>>
			<input type='hidden' name='parentPath' value=<?php  echo $_GET['parentPath'];?>>
		</table>
	</form>
</body>
</html>
<?php
	if(isset($_GET['ok'])){
		
		$setName=$_GET['setName'];
		
		//反序列化获取操作对象
		$handle=fopen("./serialize.txt","r+");
		$serialize=fread($handle,filesize("./serialize.txt"));
		fclose($handle);
		$ossClient=unserialize($serialize);
		
		if($setName){
			$saveFilePrefix=$parentPath.$setName;
			reNameFile($ossClient,$bucket,$filePath,$saveFilePrefix);
			echo "<script>alert('文件重命名成功');location='storeRecords.php?query=ok&bucket={$bucket}&parentPath={$parentPath}'</script>";
		}
		else{
			echo "<script>alert('文件名格式不正确')</script>";
		}
	
		
	}

?>