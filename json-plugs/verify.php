<?php 
error_reporting(E_ALL);

$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$user_id=isset($_POST['u'])?$_POST['u']:$_GET['u'];
$hash=isset($_POST['h'])?$_POST['h']:$_GET['h'];
if(!empty($user_id)&&!empty($hash)){
	$selectSQL="SELECT * FROM user_profile WHERE user_id=".$user_id.
			" AND user_hash='".$hash."'".
			" AND removed='N'";
	$get_user = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_user=mysql_fetch_assoc($get_user)){
		if($row_get_user['status']!='A'){
			echo "Welcome".$row_get_user['last_name'].",<br/>";
			$updateSQL = "UPDATE user_profile SET status = 'A' WHERE user_id=".$user_id;
			$result=mysql_query_or_die($updateSQL, $useradmin);
			echo "Activation complete!<br/>";
		}else{
			echo "You have been activated. You don't have to do it again!<br/>";
		}
		echo '<a href="javascript:window.close();">Close</a>';
	}else{
		echo "we didn't find you";
	}
}
?>