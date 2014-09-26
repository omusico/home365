<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Home365-经纪页面</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="/jquery/jquery-1.11.0.min.js" type="text/javascript"></script>
<style>
#realtor_input, #realtor_detail{
	padding:20px;
}
.user_head{
	width:50px;
}
#realtor_search{
	padding:20px;
	margin:50px 0;
	width:90%;
}
a.button{
	text-align:center;
	width:100%;
	background:#333333;
	border-radius:5px;
	color:#ffffff;
	text-decoration:none;
	padding:8px;
	cursor:pointer;
}
a.button:hover{
	background:#d84949;
}
</style>
</head>
<body>
<?php
require_once('social_media/google.php');
require_once 'session/initialize.php';
//print_r($_SESSION);
if(isset($_SESSION['user_id'])){
	echo "Welcome:".$_SESSION['name']."<br/>";
?>
<img class="user_head" src="<?php echo $_SESSION['picture']?>"/>
<a href="/realtor_logoff.php">退出</a>

<fieldset id="realtor_search">
<legend>查找并匹配经纪</legend>
<div id="realtor_input">
    <label for="realtor_id_input">输入经纪ID</label>
    <input id="realtor_id_input" type="text" />
    <a class="button" onclick="search_agent()">查找</a>
</div>
<div id="realtor_detail">
	<ul>
        <li>
        <label for="realtor_id">经纪ID</label>
        <input type="text" id="realtor_id" readonly>
        </li>
        <li>
        <label for="name">经纪名称</label>
        <input type="text" id="name" readonly>
        </li>
        <li>
        <label for="company">经纪公司</label>
        <input type="text" id="company" readonly>
        </li>
        <li>
        <label for="phone">电话号码</label>
        <input type="text" id="phone" readonly>
        </li>
        
        
        
    </ul>
    <a class="button" id="match_agent" onclick="match_agent()" style="display:none" >匹配</a>
</div>

</fieldset>

<?php
}else{
	echo '<a href="'.$google_login_url.'"><img src="social_media/icons/sign-in-button.png"/></a>';
}
?>
	
</body>
<script type="text/javascript">
function search_agent(){
	var realtor_id = $('#realtor_id_input').val().trim();
	if(realtor_id.length>0){
		var post_data = {}
		post_data['realtor_id']= realtor_id;
		$.post('/server/search_agent.php', post_data, search_agent_return, 'json');
	}else{
		alert('You must enter realtor id');
	}
	
}
function search_agent_return(){
	var returnArray = arguments[0];
	if(returnArray['results_found']==1){
		var realtor = returnArray['realtor_list'][0];
		console.info(realtor);
		$('#realtor_id').val(realtor['realtor_id']);
		$('#name').val(realtor['name']);
		$('#company').val(realtor['company']);
		$('#phone').val(realtor['phone']);
		if(realtor['google_id']!=null){
			alert("抱歉，此经纪已被他人注册。");
		}else{
			$('#match_agent').show();
		}
	}else{
		alert("抱歉，未找到任何结果");
	}
}
function match_agent(){
	var user_g_id = String(<?php echo $_SESSION['user_id'];?>);
	var user_realtor_id = $('#realtor_id').val().trim();
	
	if(user_g_id.length>0 && user_realtor_id.length>0){
		var post_data = new Object();
		post_data['google_id']=user_g_id;
		post_data['realtor_id']=user_realtor_id;
		//TODO
	}else if(user_g_id.length<=0){
		alert("未找到用户google id");
	}else{
		alert("未找到用户的realtor id");
	}
}
</script>
</html>