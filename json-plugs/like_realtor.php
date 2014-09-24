<?php 

$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$device_id=isset($_POST['device_id'])?$_POST['device_id']:$_GET['device_id'];
echo $device_id;
?>