<?php 
//error_reporting(E_ALL);

$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$email=isset($_POST['email'])?$_POST['email']:$_GET['email'];
//echo $email;
if(!empty($email)){
	$user = get_user_detail($email);
	if(empty($user)){
		$return_code=2;
		$error="email not registered";
	}else{
		if($user['status']=='D'){
			$return_code=1;
			$error="email not active";
		}else{
			if(send_mail($user)){
				$return_code=0;
				$error="Email sent";
			}else{
				$return_code=4;
				$error="error sending email";
			}
		}
	}
}else{
	$return_code=5;
	$error="email not provided";
}

$return_array=array("return_code"=>$return_code,"error"=>$error);
echo json_encode($return_array);

function get_user_detail($email){
	global $useradmin;
	$selectSQL = "SELECT * FROM user_profile WHERE email='".$email."' AND removed ='N'";
	$get_user = mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_user=mysql_fetch_assoc($get_user)){
		return $row_get_user;
	}else{
		return false;
	}
}
$current_GMT_time=get_GMT(time());
$current_date_time_string=date("Y-m-d H:i:s",$current_GMT_time);


function send_mail($user){
	global $email;
	$last_name=$user['last_name'];
	$user_id=$user['user_id'];
	$gender=$user['gender'];
	require_once'phpmailer/PHPMailerAutoload.php';
	$mail=new PHPMailer;
	$mail->isSMTP();
	//$mail->SMTPDebug = 1;
	$mail->Host = 'host.zhidaomedia.com';
	$mail->Port = 465;
	$mail->SMTPAuth = true;
	$mail->Username = 'home365';
	$mail->Password = '6042845458';
	$mail->SMTPSecure = 'ssl';
	$mail->From = 'service@home365.ca';
	$mail->FromName = 'Mailer';
	$mail->addAddress($email);
	$mail->isHTML(true);
	$mail->WordWrap=50;
	$mail->CharSet = "UTF-8";
	$mail->Subject = $last_name.($gender=='M'?'先生':'小姐').", 忘了密码？";
	$mail->Body=$last_name.($gender=='M'?'先生':'小姐').'您好！<br/><br/><br/>忘记密码了？不用担心。<br/><br/>请尽快点击下面的链接，更改您的登录密码<br/><br/><a href="http://www.home365.ca/json-plugs/change_password.php?e='.$email.'&h='.$user['user_hash'].'">更改密码</a><br/><br/>如果您有任何疑问，请勿回复此邮件，但欢迎随时通过下面的邮箱联系我们：<br/>zhidaomedia@gmail.com<br/><br/><br/>再次感谢你使用地产知道。<br/><br/><br/>地产知道开发团队';

	if(!$mail->send()) {
		return false;
		//$error="failed to send user activation email<br/>Error:".$mail->ErrorInfo;
	}else{
		return true;
		//$return_code=0;
		//$error="change password email sent";	
	}
}
?>