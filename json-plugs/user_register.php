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
$lastname=isset($_POST['lastname'])?$_POST['lastname']:$_GET['lastname'];
$password=isset($_POST['password'])?$_POST['password']:$_GET['password'];
$email=isset($_POST['email'])?$_POST['email']:$_GET['email'];
$gender=isset($_POST['gender'])?$_POST['gender']:$_GET['gender'];
$user_hash=sha1(mt_rand(10000,99999).time().$email);
$current_GMT_time=get_GMT(time());
$current_date_time_string=date("Y-m-d H:i:s",$current_GMT_time);

if(!empty($email)){
	$selectSQL = "SELECT * FROM user_profile WHERE email ='".$email."'";
	$get_user=mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_user=mysql_fetch_assoc($get_user)){
		$return_code=1;
		$error="email has been registered";
	}else{
		if(!empty($lastname)&&!empty($gender)){
			$insertSQL = sprintf("INSERT INTO user_profile(last_name, password, gender, email, user_hash, date_registered)VALUES(%s,%s,%s,%s,%s,%s)",
						GetSQLValueString($lastname,"text"),
						GetSQLValueString($password,"text"),
						GetSQLValueString($gender,"text"),
						GetSQLValueString($email,"text"),
						GetSQLValueString($user_hash,"text"),
						GetSQLValueString($current_date_time_string,"date")
						);
			$result=mysql_query_or_die($insertSQL,$useradmin);
			$user_id=mysql_insert_id();
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
			$mail->Subject = $lastname.($gender=='m'?'先生':'小姐')."，感谢您注册地产知道账号";
			$mail->Body=$lastname.($gender=='m'?'先生':'小姐').',您好！<br/><br/><br/>非常感谢您注册了地产知道账号，未来您可以用这个账号来参加我们组织的线下活动，并享受一些会员才有的优惠。<br/><br/>不过完成注册还剩下最后一步——请点击下面的链接，激活您的账号<br/><br/><a href="http://www.home365.ca/json-plugs/verify.php?u='.$user_id.'&h='.$user_hash.'">激活账号</a><br/><br/>如果您有任何疑问，请勿回复此邮件，但欢迎随时通过下面的邮箱联系我们：<br/>zhidaomedia@gmail.com<br/><br/><br/>再次感谢你使用地产知道。<br/><br/><br/>地产知道开发团队';

			
			if(!$mail->send()) {
				$return_code=4;
				$error="failed to send user activation email<br/>Error:".$mail->ErrorInfo;
			}else{
				$return_code=0;
				$error="User registration successful. An activation email has been sent to the email address you provided. Please check your email inbox and activate your account. If you can't see it in your inbox, it probably will be at your junk mail box";	
			}
		}else{
			$return_code=2;
			$error="last name and gender cannot be empty";
		}
	}
}else{
	$return_code=3;
	$error="email cannot be empty";
}
$return_array=array("return_code"=>$return_code,"error"=>$error);
echo json_encode($return_array);
?>