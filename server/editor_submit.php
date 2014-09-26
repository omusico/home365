<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
define(MAX_IMAGE_SIZE, 2000*1024);//Max size=2MB

$photo_name=$_FILES['photo']['name'];
$photo_type=$_FILES['photo']['type'];
$photo_temp_name=$_FILES['photo']['tmp_name'];
$error=$_FILES['photo']['error'];
$photo_size=$_FILES['photo']['size'];
if(isset($error)&&$error!=''){
	$return_code=10;
	$return_message=$error;
}else{
	if($photo_size>MAX_IMAGE_SIZE){
		$return_code=1;
		$return_message="file too large";
	}else{
		if ($photo_type!= "image/gif" && $photo_type!= "image/jpeg" && $photo_type!= "image/pjpeg" && $photo_type!= "image/png"){
			$return_code = 2;
			$return_message ="file type ".$photo_type."not supported";
		}else{
			$document_root='/home/home365/public_html';
	  		error_reporting(E_ALL);
	 		require_once $document_root.'/CDN/cdnConnect.php';
			$ostore = $cloud->ObjectStore();
			if(!$ostore){
				$return_code=3;
				$return_message="error creating cloud object store";
			}else{
				$container=$ostore->Container($_containerName);
				$mypicture = $container->DataObject();
				try {
					// this will fail if the container doesn't exist
					$cloud_file_name='blog_'.$photo_name;
					$mypicture->Create(
							array('name'=>$cloud_file_name, 'content_type'=>$photo_type),
							$photo_temp_name);
					$cloud_file_url=$_home365domain.'/'.$cloud_file_name;
					$return_code=0;
					$return_message=$cloud_file_url;
				}catch(Exception $e){
					$return_code=4;
					$return_message=$e->getMessage();
				}
			}
		}
	}

}
echo $return_code.'::'.$return_message;
?>

</body>
</html>
