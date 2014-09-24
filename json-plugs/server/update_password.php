<?php 
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$password=$_POST['password'];
$email=$_POST['email'];
$user_id=$_POST['user_id'];
if(empty($password)){
	$return_code=1;
	$error="password is empty";
}else{
	$updateSQL = sprintf("UPDATE user_profile SET password=%s WHERE user_id=%s",
	GetSQLValueString($password,"text"),
	GetSQLValueString($user_id,"int"));
	$result = mysql_query_or_die($updateSQL,$useradmin);
	$return_code=0;
	$error="password changed successfully";
}
$return_array=array("return_code"=>$return_code, "error"=>$error);
echo json_encode($return_array);
?>
