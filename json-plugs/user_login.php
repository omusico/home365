<?php 

$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

$email=isset($_POST['email'])?$_POST['email']:$_GET['email'];
$password=isset($_POST['password'])?$_POST['password']:$_GET['password'];

if(!empty($email)&&!empty($password)){
	$selectSQL="SELECT * FROM user_profile WHERE email='".$email."'".
			" AND removed='N'";
	$get_user=mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_user=mysql_fetch_assoc($get_user)){
		if($row_get_user['status']=='D'){
			$return_code=4;
			$error="Email not activated";
		}else{
			if($row_get_user['password']==$password){
				$return_code=0;
				$error="Success";
				$user_id=$row_get_user['user_id'];
				$gender=$row_get_user['gender'];
				$last_name=$row_get_user['last_name'];
			}else{
				$return_code=3;
				$error="Password not correct";
			}
		}
	}else{
		$return_code=1;
		$error="No email address found in database";
	}
}else{
	$return_code=2;
	$error="email or password missing";
}
$return_array=array("return_code"=>$return_code,"user_id"=>$user_id,"error"=>$error, "gender"=>$gender, "last_name"=>$last_name);
echo json_encode($return_array);
?>