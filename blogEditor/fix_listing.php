<?php 
$document_root='/home/home365/public_html';
$domain_name='http://www.home365.ca/';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="../jquery/jquery-1.11.0.min.js" type="text/javascript"></script>
<title>修复地产信息</title>
<style>
.toolbar{
	margin:20px 5px 20px 5px;
}
a.button{
	text-align: center;
	width: 100%;
	background: #333333;
	border-radius:5px;
	color: #ffffff;
	text-decoration: none;
	font-size: 14px;
	font-family: 'Raleway',sans-serif;
	font-weight: bold;
	text-transform: uppercase;
	padding: 10px 10px 10px 10px;
	margin:5px;
}
a:hover{
	text-decoration: none;
	background:#d84949;
}
input[type="text"]{ 
	width:100px;
	padding: 10px 10px; 
	line-height: 5px; 
	font-size:18px;
}
textarea{
	width: 540px;
	height: 320px;
	border: 3px solid #cccccc;
	padding: 5px;
	font-family: Tahoma, sans-serif;
	background-position: bottom right;
	background-repeat: no-repeat;
	margin-top:50px;
}
#return_result{
	width: 900px;
	border: 3px dotted #cccccc;
	margin-top: 50px;
	padding:50px 20px;
}

</style>
</head>

<body>
<div class="toolbar">
<label for="mls_input">输入listing的MLS号码：</label>
<input id="mls_input" type="text">
<a class="button" onclick="fetch_data()">获取信息</a>
</div>

<div id="return_result" style="display:none"></div>
</body>
<script type="text/javascript">
function fetch_data(){
	var mls = $('#mls_input').val().trim();
	var post_data = new Object();
	post_data['mls']=mls;
	$.post('../fetch_listing.php', post_data, fetch_data_return, 'html');
	$('#return_result').empty();
	$('#return_result').append("加载中，请稍候(最长可能要30秒)...");
	if($('#return_result').hide()){
		$('#return_result').show();
	}
}
function fetch_data_return(){
	var return_data = arguments[0];
	$('#return_result').empty();
	$('#return_result').append(return_data);
	$('#return_result').show();
}
</script>
</html>
