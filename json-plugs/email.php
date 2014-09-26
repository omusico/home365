<?php 
error_reporting(E_ALL);
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

$mail->From = 'info@home365.ca';
$mail->FromName = 'Mailer';
$mail->addAddress('james.k.zhao@gmail.com','James');
$mail->isHTML(true);
$mail->WordWrap=50;

$mail->Subject="Here is the subject";
$mail->Body='This is the HTML message body in <b>in bold!</b>';

if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo 'Message has been sent';

?>
