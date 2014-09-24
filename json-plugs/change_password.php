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
$email=isset($_POST['e'])?$_POST['e']:$_GET['e'];
$hash=isset($_POST['h'])?$_POST['h']:$_GET['h'];
$user=get_user_detail($email);
//var_dump($user);
if(!empty($user)){
	$user_id=$user['user_id'];
}
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
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>修改密码</title>
<script src="../../jquery/jquery-1.11.0.min.js" type="text/javascript"></script>
<style>
filedset{
	width:100%;
	margin:auto;
}
#container{
	max-width:500px;
	text-align:center;
	margin:auto;
}
button{
	background-color:#8BAF47;
	border:1px solid #8BAF47;
	border-radius:6px;
	padding:5px 10px;
	cursor:pointer;
	color:#fff;
	font-size:20px;
	margin:20px auto;
}
</style>
</head>

<body>
<div id="container">
<fieldset>
<legend>修改密码</legend>
	<p>
		输入新密码：<input id="new_password" type="password"onMouseOut="check_password()"><span style="color:#FF0000;">*</span>
    </p>
    <p>
    	确认新密码：<input id="confirm_password" type="password" onMouseOut="check_password()"><span style="color:#FF0000;">*</span>
        
    </p>    
    <p>
    	<span id="hint" style="color:#ff0000;"></span>
    </p>
<button id="submit" onclick="submit_password()">提交</button>
</fieldset>

</div>
<script type="text/javascript">
function check_password(){
	var password=$('#new_password').val().trim();
	var confirm_password=$('#confirm_password').val().trim();
	if(password==confirm_password){
		$('#hint').text('');
		return true;
	}else{
		$('#hint').text("密码输入不一致");
		return false;
	}
}
function submit_password(){
	if(check_password()){
		var password=$('#new_password').val().trim();
		var confirm_password=$('#confirm_password').val().trim();
		var post_data=new Object;
		post_data['password']=password;
		post_data['email']='<?php echo $email;?>';
		post_data['user_id']=<?php echo $user_id;?>;
		//alert(post_data['password']);
		$.post('server/update_password.php',post_data,update_password_return,'json');
	}else{
		alert("密码输入前后不一致");
	}
}
function update_password_return(data){
	var return_data=arguments[0];
	var return_code = return_data.return_code;
	var return_msg = return_data.error;
	alert(return_msg);
	if(return_code==0){
		/*if(confirm("退出？")){
			window.open('','_self');
			window.close();
		}*/
	}else{
	}
}
</script>
</body>
</html>