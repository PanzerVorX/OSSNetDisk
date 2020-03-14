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
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
		
		body{
			/*background-color:#363636;*/
			background-color: #272822;
		}

		span{
			font-size: 30px;
			font-weight: bold;
			color: #26A3DB;
		}

		h1{
		 color: #26A3DB;
		}
		
		form{
			margin-top: 10px;
			margin-left: 20px;
		}

		.fm1{
			margin-top: 0px;
			margin-left: 20px;
		}

		input[type='text']{
			width:250px;
			height: 26px;
			font-size: 25px;
			font-weight: bold;
		}

		input[type='submit']{
			width:100px;
			height: 30px;
			font-size: 20px;
			font-weight: bold;
			margin-left: 10px;
		}

		input[type='file']{
			width:90px;
			height: 30px;
			font-size: 18px;
			font-weight: bold;
		}
		
		.query{
			width:100px;
			height: 30px;
			font-size: 20px;
			font-weight: bold;
			margin-left: 10px;
		}

		.fm2{
			position: absolute;
			margin-left: 515px;
			top: 44px;
			outline:5px solid #00f;
		}
		
		.splitDiv{
			margin-top: 16px;
			background-color:#888;
			height: 5px;
		}
		
		table{
			margin:auto;
			background-color:#80B5EB;
		}

		table td{
			border: 2px solid #00f;
			text-align: center;
			width: 250px;
			height: 30px;
			font-size: 16px;
			font-weight: bold;
		}

		.checkbox_td{
			text-align: left;
		}

		input[type='checkbox']{
			height: 18px;
			width: 18px;
		}

		a{
			text-decoration: none;
			color: #05558a;// #572084;
		}

		a:hover{
			text-decoration: underline;
			color:#e3e74b; // #458B00;
		}

		#uploadSingleFile{
			width:130px;
		}

		.uploadMultipleFiles{
			width:120px;
			height: 30px;
			font-size: 20px;
			font-weight: bold;
			position: absolute;
			margin-top: -33px;
			margin-left: 780px;
			outline:5px solid #00f;
		}

		.batchDiv{
			position: absolute;
			right: 2%;
			top: 9%;
			outline:5px solid #00f;
		}

		.batchDiv input[name='batchDownload']{
			margin-left: 0px;
		}

		#backParentPath{
			width: 200px;
			height: 38px;
			font-size: 20px;
			font-weight: bold;
			border-radius: 10px;
			position: absolute;
			right: 12%;
			top: 23.5%;
			border: 5px solid #00f;
		}

		#createDir{
			width: 110px;
			height: 38px;
			font-size: 20px;
			font-weight: bold;
			border: 5px solid #00f;
			position: absolute;
			right: 30%;
			top: 23.5%;
		}

		img{
			width: 26px;
			height: 24px;
			margin-right: 8px;
		}
		
		.totalDiv{
			position:relative;
		}
		
		.notice{
			font-size: 30px;
			font-weight: bold;
			color: #26A3DB;
			position:absolute;
			top:0px;
			left:90%;
		}

	</style>
	
		<script type="text/javascript">
		
		
		function getXmlObject(){//获取AJAX操作对象
			
			var XMLHttp=null;
			if(window.XMLHttpRequest){
				XMLHttp=new XMLHttpRequest();
			}
			else if(window.ActiveXObject){
				try{
					XMLHttp=new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e){
					XMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
			}
			return XMLHttp;
		}

		
		
		function queryRecord(bucket,parentPath){
			
			var XMLHttp=getXmlObject();
			//设置AJAX交互的数据库分页查询页面与相关参数
			var url="storeRecordsAjax.php?query=ok&bucket="+bucket+"&"+"parentPath="+parentPath;
			XMLHttp.open("GET",url,true);
			XMLHttp.send(null);
			
			XMLHttp.onreadystatechange=function(){//查询后的响应方法
				if(XMLHttp.readyState==4&&XMLHttp.status==200){//判断处理服务器响应状态
					//JS中标签元素的下属内容：标签元素对象的innerHTML属性
					document.body.innerHTML=null;
					document.body.innerHTML=XMLHttp.responseText;//内容区域显示查询结果记录；
				}
			}
			
		}
		
		function queryRecordBlurry(){
			
			var  bucket = (document.getElementsByName("bucket1"))[0].value;
			var  parentPath=(document.getElementsByName("parentPath1"))[0].value;
			var  fileName=(document.getElementsByName("fileName"))[0].value;
			
			var XMLHttp=getXmlObject();
			var url="storeRecordsAjax.php?query=ok&bucket="+bucket+"&"+"parentPath="+parentPath+"&fileName="+fileName;
			XMLHttp.open("GET",url,true);
			XMLHttp.send(null);
			
			XMLHttp.onreadystatechange=function(){//查询后的响应方法
				if(XMLHttp.readyState==4&&XMLHttp.status==200){//判断处理服务器响应状态
					//JS中标签元素的下属内容：标签元素对象的innerHTML属性
					document.body.innerHTML=null;
					document.body.innerHTML=XMLHttp.responseText;//内容区域显示查询结果记录；
				}
			}
		}
		
		
		</script>
	
</head>
<body>
	<div id='totalDiv'>
	<span>用户名：<a href='userInfo.php?query=ok&bucket=<?php echo $bucket;?>&parentPath=<?php echo @$_GET["parentPath"];?>'><?php echo $bucket;?></a></span></br>
	<div class='notice'><a href='noticeList.php?query=ok&bucket=<?php echo $bucket;?>&parentPath=<?php echo @$_GET["parentPath"];?>'>站内通告</a></div>
	<form method="get" name='fm1' class="fm1">
		<span>文件名：</span><input type="text" name="fileName" placeholder='模糊查询'><input type="button"  onclick="queryRecordBlurry()" class="query" name="query" value="查询">
		<input type="hidden" name="bucket1" value=<?php echo $bucket;?>>
		<input type="hidden" name="parentPath1" value=<?php echo @$_GET['parentPath'];?>>
	</form>
	<form  name='fm2' class='fm2' method="post" action="uploadSingleFile.php" enctype="multipart/form-data">
		<input type="file" name="uploadFile">
		<input type="hidden" name="bucket" value=<?php echo $bucket;?>>
		<input type="hidden" name="parentPath" value=<?php echo @$_GET["parentPath"];?>>
		<input id='uploadSingleFile' type="submit" name="upload" value="单文件上传">
	</form>
	<input class='uploadMultipleFiles' type="button" name="uploadMultipleFiles" value='多文件上传' onclick=location='uploadMultipleFiles.php?bucket=<?php echo $bucket;?>&parentPath=<?php echo @$_GET["parentPath"];?>'>
	<div class='splitDiv'></div>

<?php
	echo"<form name='fm3' method='post' action='batchOperation.php'>";
	echo "<div class='batchDiv'>";
	echo "<input type='submit' name='batchDownload' value='批量下载'>";
	echo "<input type='submit' name='batchDelete' value='批量删除'>";
	echo "<input type='hidden' name='bucket' value={$bucket}>";
	echo "</div>";
	if(isset($_GET['query'])){

		//查询指定目录下的文件名
		$fileName=@$_GET['fileName'];
		$parentPath=$_GET['parentPath'];
		$filePath=$parentPath.$fileName;
		//var_dump($filePath);

		echo "<input type='hidden' name='parentPath' value={$parentPath}>";
		echo "<h1>当前目录：根目录/".$parentPath."</h1>";//当前目录

		//获取上一级目录
		$isNotRoot=strpos($parentPath,'/');//当前目录是否为根目录		
		if($isNotRoot){
			$temp=substr($parentPath,0,strrpos($parentPath,'/'));
			$isNotSecondDir=strpos($temp,'/');
			if($isNotSecondDir){//判断是否为次级目录
				$backParentPath=substr($temp,0,strrpos($temp,'/')+1);
			}
			else{
				$backParentPath='';
			}
		}
		else{
			$backParentPath='';
		}
		echo "<input id='createDir' type='button' name='createDir' value='创建目录' onclick=location='createDir.php?createDir=ok&bucket={$bucket}&parentPath={$parentPath}'>";
	?>
		<input type='button' id='backParentPath' name='backParentPath' value='返回上一级' onclick="queryRecord('<?php echo $bucket;?>','<?php echo $backParentPath;?>')">
<?php

		$tableName=$bucket."_admin";//表名
		$pdo=new PDO('mysql:host=localhost;dbname=test','root','19450902');
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		$pdo->exec('set names utf8');
		$sql="select * from {$tableName} where file_path like '$filePath%';";
		$smt=$pdo->query($sql);
		$rows=$smt->fetchAll();
		if($rows){
				$dirArr=array();
		    	echo "<table>";
				if(  ($parentPath !='' && count($rows)>1) || ($parentPath=='' && count($rows)>=1) )
		    		echo "<tr><td>文件名</td><td>文件大小</td><td>最后修改时间</td><td colspan='3'>操作</td></tr>";
					foreach ($rows as $row) {
				
					$object=$row['file_path'];
					$showTime=$row['last_date'];
					$showFileSize=$row['file_size'];
					$fileType=$row['file_type'];
					
					//获取文件路径除去父目录之后的部分
			    	if($parentPath){
			    		$tempStr=substr($object,strlen($parentPath));
			    	}
			    	else{
			    		$tempStr=$object;
			    	}
					
					
					$isDir=strpos($tempStr,'/');//判断是否是当前目录下的直接子文件
			    	if(!$isDir){
			    		if($tempStr){
			    			if(strpos($fileType,'image')===0){
			    				$imageUrlStr="<a href='frameOperationImg.php?operationImg=ok&bucket={$bucket}&object={$object}&parentPath={$parentPath}'><img src='iconImg/img_pic.png'>{$tempStr}</a>";
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}>{$imageUrlStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif (strpos($fileType,'audio/mp3')===0 || strpos($fileType,'audio/mpeg')===0){
			    				$audioUrlStr="<a href='operationAudioAndVideo.php?operationAudio=ok&bucket={$bucket}&parentPath={$parentPath}&object={$object}'><img src='iconImg/img_audio.png'>{$tempStr}</a>";
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}>{$audioUrlStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif(strpos($fileType,'video')===0){
			    				$videoUrlStr="<a href='operationAudioAndVideo.php?operationVideo=ok&bucket={$bucket}&parentPath={$parentPath}&object={$object}'><img src='iconImg/img_video.png'>{$tempStr}</a>";
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}>{$videoUrlStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif (strpos($tempStr,'.txt')) {
			    				$txtUrlStr="<a href='operationTxt.php?operationTxt=ok&bucket={$bucket}&parentPath={$parentPath}&object={$object}'><img src='iconImg/img_txt.png'>{$tempStr}</a>";
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}>{$txtUrlStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif((strrpos($tempStr,'.xlsx')==(strlen($tempStr)-5))||(strrpos($tempStr,'.xls')==(strlen($tempStr)-4))){
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}><img src='iconImg/img_excel.png'>{$tempStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif((strrpos($tempStr,'.ppt')==(strlen($tempStr)-4)) || (strrpos($tempStr,'.pptx')==(strlen($tempStr)-5))){
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}><img src='iconImg/img_ppt.png'>{$tempStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif((strrpos($tempStr,'.doc')==(strlen($tempStr)-4)) || (strrpos($tempStr,'.docx')==(strlen($tempStr)-5))){
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}><img src='iconImg/img_word.png'>{$tempStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			elseif((strrpos($tempStr,'.rar')==(strlen($tempStr)-4)) || (strrpos($tempStr,'.zip')==(strlen($tempStr)-4))){
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}><img src='iconImg/img_package.png'>{$tempStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    			else{
			    				echo "<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value={$object}><img src='iconImg/img_file.png'>{$tempStr}</td><td>{$showFileSize}</td><td>{$showTime}</td><td><a href='downloadFile.php?download=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket={$bucket}&filePath={$object}'>删除</a></td><td><a href='rename.php?rename=ok&bucket={$bucket}&filePath={$object}&parentPath={$parentPath}'>重命名</a></td></tr>";
			    			}
			    		}
			    	}
			    	else{
			    		$dirName=substr($tempStr,0,strpos($tempStr,'/')+1);
			    		if(!in_array($dirName,$dirArr)){
			    			$dirArr[]=$dirName;//可使用数组存储首次出现的值进行防重复值处理
?>
							<tr><td class='checkbox_td'><input type='checkbox' name='fileArr[]' value=<?php echo $object;?>><a href='javascript:void(0)' id='btn1' onclick="queryRecord('<?php  echo $bucket;?>','<?php echo $object;?>');" ><img src='iconImg/img_dir.png'><?php echo $tempStr; ?></a></td><td><?php echo $showFileSize; ?></td><td><?php echo $showTime; ?></td><td><a href='downloadFile.php?download=ok&bucket=<?php  echo $bucket;?>&filePath=<?php echo $object; ?>&parentPath=<?php echo $parentPath;?>'>下载</a></td><td><a href='deleteFile.php?deleteFile=ok&bucket=<?php echo $bucket;?>&filePath=<?php echo $object;?>'>删除</a></td><td><a href='rename.php?rename=ok&bucket=<?php echo $bucket;?>&filePath=<?php echo $object;?>&parentPath=<?php echo $parentPath;?>'>重命名</a></td></tr>
<?php
			    		}
			    	}
	
				}
				echo "</table>";
		}
	}
?>
	</form>
	</div>
	</body>
	</html>

