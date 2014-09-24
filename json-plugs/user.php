<?php 
$action=isset($_POST['action'])?$_POST['action']:$_GET['action'];
if($action=='login'){
	require_once('user_login.php');
}elseif($action=='register'){
	require_once('user_register.php');
}elseif($action=='password'){
	require_once('retrieve_password.php');
}elseif($action=='like'){
	require_once('like_realtor.php');
}else{
	$return_code=10;
	$return_message='action not recognized';
	$return_array=array("return_code"=>$return_code,"return_message"=>$return_message);
	echo json_encode($return_array);
}
?>