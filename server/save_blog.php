<?php 
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

$current_GMT_time=get_GMT(time());
$current_date_time_string=date("Y-m-d H:i:s",$current_GMT_time);
$current_YVR_time=getTimeByCity($current_GMT_time,'YVR');
$current_YVR_time_string=date("Y-m-d H:i:s", $current_YVR_time); 

$blog_id=isset($_POST['blog_id'])?$_POST['blog_id']:$_GET['blog_id'];
$blog_title=isset($_POST['blog_title'])?$_POST['blog_title']:$_GET['blog_title'];
$blog_source=isset($_POST['blog_source'])?$_POST['blog_source']:$_GET['blog_source'];
$blog_content=isset($_POST['blog_content'])?$_POST['blog_content']:$_GET['blog_content'];
$blog_media_url=isset($_POST['media_url'])?$_POST['media_url']:$_GET['media_url'];
$post_blog=isset($_POST['post_blog'])?$_POST['post_blog']:$_GET['post_blog'];
$user_id=100;
$blog_title=preg_replace('/(<([^>]+)>)/','',$blog_title);
$blog_content=preg_replace('/\<br\040*\/?>\>/',chr(10),$blog_content);
if(empty($blog_id)){
	$insertSQL=sprintf("INSERT INTO blogs (user_id, blog_title, blog_source, blog_content, blog_media_url, date_created_UTC, date_created_YVR, date_updated_UTC, date_updated_YVR) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s)",
	GetSQLValueString($user_id,"int"),
	GetSQLValueString($blog_title,"text"),
	GetSQLValueString($blog_source,"text"),
	GetSQLValueString($blog_content,"text"),
	GetSQLValueString($blog_media_url,"text"),
	GetSQLValueString($current_date_time_string,"date"),
	GetSQLValueString($current_YVR_time_string,"date"),
	GetSQLValueString($current_date_time_string,"date"),
	GetSQLValueString($current_YVR_time_string,"date")
	);
	$result=mysql_query_or_die($insertSQL, $useradmin);
	$blog_id=mysql_insert_id($useradmin);
	$return_code=0;
	$return_message="储存完毕";
}else{
	$updateSQL = sprintf("UPDATE blogs SET blog_title=%s, blog_source=%s, blog_content=%s, blog_media_url=%s, date_updated_UTC=%s , date_updated_YVR=%s WHERE blog_id=%s",
	GetSQLValueString($blog_title,"text"),
	GetSQLValueString($blog_source,"text"),
	GetSQLValueString($blog_content,"text"),
	GetSQLValueString($blog_media_url,"text"),
	GetSQLValueString($current_date_time_string,"date"),
	GetSQLValueString($current_YVR_time_string,"date"),
	GetSQLValueString($blog_id,"int")
	);
	$result=mysql_query_or_die($updateSQL,$useradmin);
	$return_code=0;
	$return_message="更新完毕";
}
if($return_code==0&&$post_blog=='Y'){
	$updateStatus = sprintf("UPDATE blogs SET status=%s WHERE blog_id=%s",
	GetSQLValueString('P',"text"),
	GetSQLValueString($blog_id,"int"));
	$result=mysql_query_or_die($updateStatus,$useradmin);
	$return_message="发布完毕";
}

$return_array=array("blog_id"=>$blog_id,"return_code"=>$return_code,"return_message"=>$return_message);
echo json_encode($return_array);
?>