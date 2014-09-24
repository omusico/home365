<?php 
//error_reporting(E_ALL);
$document_root='/home/home365/public_html';
$domain_name='http://www.home365.ca/';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$current_GMT_time=get_GMT(time());
$current_GMT_time_string=date("Y-m-d H:i:s",$current_GMT_time);
$current_YVR_time=getTimeByCity($current_GMT_time,'YVR');
$current_YVR_time_string=date("Y-m-d H:i:s",$current_YVR_time);

$search_type=isset($_POST['search_type'])?$_POST['search_type']:$_GET['search_type'];
$keyword=isset($_POST['keyword'])?$_POST['keyword']:$_GET['keyword'];
$userState=isset($_POST['userState'])?$_POST['userState']:$_GET['userState'];
$deviceID=isset($_POST['deviceID'])?$_POST['deviceID']:$_GET['deviceID'];
switch($search_type){
	case 'property':
	require_once 'property_search.php';
	break;
	case 'blog':
	require_once 'blog_search.php';
	break;
	case 'realtor':
	require_once 'realtor_list.php';
	break;
	case 'home_banner':
	require_once 'banner_list.php';
	break;
	case 'realtor_properties':
	require_once 'realtor_properties.php';
	break;
}
if(!empty($keyword)){
	require_once 'keyword_search.php';
}
if(!empty($userState)&&!empty($deviceID)){
	$insertSQL = sprintf("INSERT INTO visitor_log (device_id, user_state, time_stamp) VALUES (%s, %s, %s)",
	GetSQLValueString($deviceID,"text"),
	GetSQLValueString($userState,"text"),
	GetSQLValueString($current_YVR_time_string,"date"));
	$result = mysql_query_or_die($insertSQL, $useradmin);
}
?>
