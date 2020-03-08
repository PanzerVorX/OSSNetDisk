<?php
	
	require "aliyun-oss-php-sdk-2.3.0/autoload.php";
	use OSS\OssClient;
	use OSS\Core\OssException;
	use OSS\Http\RequestCore;
	use OSS\Http\ResponseCore;
	
	function getShowFileSize($fileByte){
		$showFileSize=$fileByte."byte";
		if(($fileByte>=1024) && ($fileByte<(1024*1024))){
			$showFileSize=intval($fileByte/1024);
			$showFileSize.='KB';
		}
		elseif(($fileByte>=(1024*1024)) && ($fileByte<1024*1024*1024)){
			$showFileSize=intval($fileByte/(1024*1024));
			$showFileSize.='M';
		}
		elseif ($fileByte>=(1024*1024*1024)) {
			$showFileSize=intval($fileByte/(1024*1024*1024));
			$showFileSize.='G';
		}
		return $showFileSize;
	}

		
		$bucket=$_GET['bucket'];
		$parentPath=$_GET['parentPath'];
		
		//反序列化
		$handle=fopen("./serialize.txt","r+");
		$serialize=fread($handle,filesize("./serialize.txt"));
		fclose($handle);
		$ossClient=unserialize($serialize);

		$prefix = '';
		$delimiter = '';
		$nextMarker = '';
		$maxkeys = 1000;
		$options = array(
		    'delimiter' => $delimiter,
		    'prefix' => $prefix,
		    'max-keys' => $maxkeys,
		    'marker' => $nextMarker,
		);
		
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$sql='set names utf8';
		$pdo->exec($sql);
		
		$tableName=$bucket."_admin";//表名
		$result = $pdo->query("show tables like '". $tableName."'");
		$row = $result->fetchAll();
		if(count($row)){//若存在则清空表
			$sql="truncate {$tableName}";
		} 
		else {//若不存在则创建表
		$sql="create table {$tableName}(
						id int auto_increment primary key,
						file_path LONGTEXT not null,
						file_size char(50) not null,
						last_date char(50) not null,
						file_type char(255) not null,
						file_bucket char(255) not null
					)engine=InnoDB default charset=gbk;";
		}
		$pdo->exec($sql);
		
		//判断管理端对于该存储空间的数据表是否存在
		$userTableName=$bucket."_user";//表名
		$result = $pdo->query("show tables like '". $userTableName."'");
		$isExistUserTable = $result->fetchAll();
		if(count($isExistUserTable)){//若存在则清空表
			$sql="truncate {$userTableName}";
			$pdo->exec($sql);
		} 

		try {
			//实现分层浏览文件：列举文件时进行分层处理，通过查询指定目录下的所有内部文件进行标识判断后只列出子文件/目录
		    $listObjectInfo = $ossClient->listObjects($bucket, $options);
		    $objectList = $listObjectInfo->getObjectList();
		    if (!empty(count($objectList))) {		    	
			    foreach ($objectList as $objectInfo) {
				
					$object=$objectInfo->getKey();
			    	$objectMeta = $ossClient->getObjectMeta($bucket, $object);//获取文件元信息
					
					//文件大小
					if(strrpos($object,'/')!=strlen($object)-1){
						$fileByte=$objectMeta['content-length'];
						$fileSize=getShowFileSize($fileByte);
					}
					else{
						$fileSize='此为目录';
					}
					
					$fileType=$objectMeta['content-type'];
			    	
			    	//修改时间
			    	$lastModified=$objectMeta['last-modified'];
			    	$lastModified=substr($lastModified,0,strrpos($lastModified,':'));
			    	$hour=substr($lastModified,strrpos($lastModified,':')-2,2);
			    	$hour=$hour+8;
					if($hour>=24){
						$hour=$hour-24;
					}
			    	$lastDate= substr_replace($objectMeta['last-modified'],$hour,strrpos($lastModified,':')-2,2);
			    	$lastDate=rtrim($lastDate,'GMT');
					
					$sql="insert into {$tableName}  (file_path,file_size,last_date,file_type,file_bucket) values (?,?,?,?,?);";
					$smt=$pdo->prepare($sql);
					$smt->bindParam(1,$object);
					$smt->bindParam(2,$fileSize);
					$smt->bindParam(3,$lastDate);
					$smt->bindParam(4,$fileType);
					$smt->bindParam(5,$bucket);
					$smt->execute();
					
					//保持用户与管理者查询记录同步
					if(count($isExistUserTable)){
						$sql="insert into {$userTableName}  (file_path,file_size,last_date,file_type,file_bucket) values (?,?,?,?,?);";
						$smt=$pdo->prepare($sql);
						$smt->bindParam(1,$object);
						$smt->bindParam(2,$fileSize);
						$smt->bindParam(3,$lastDate);
						$smt->bindParam(4,$fileType);
						$smt->bindParam(5,$bucket);
						$smt->execute();
					}
				}
			}
			echo "<script>location='showBucketFile.php?query=ok&bucket={$bucket}&parentPath={$parentPath}';</script>";
		}
		catch (OssException $e) {
			   
		}	
		
	?>