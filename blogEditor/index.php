<?php 
$document_root='/home/home365/public_html';
$domain_name='http://www.home365.ca/';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

$blog_id=isset($_POST['blog_id'])?$_POST['blog_id']:$_GET['blog_id'];
if(isset($blog_id)&&$blog_id!=''){
	$selectSQL = "SELECT * FROM blogs WHERE blog_id=$blog_id AND status='P'";
	$get_blog = mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_blog=mysql_fetch_assoc($get_blog)){
		$blogObject=$row_get_blog;
	}else{
		echo "The artile you are looking for doesn't exist anymore";
		exit();
	}
	$page_title="编辑文章";
}else{
	$blogObject=array("blog_id"=>'',"blog_content"=>'',"blog_title"=>'');
	$page_title="新建文章";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $page_title?></title>
<link rel="stylesheet" href="../jquery/jquery-ui-1.10.4.css">
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
	width:540px;
	padding: 10px 10px; 
	line-height: 10px; 
}
textarea{
	width: 540px;
	height: 320px;
	border: 3px solid #cccccc;
	padding: 5px;
	font-family: Tahoma, sans-serif;
	background-position: bottom right;
	background-repeat: no-repeat;
}
#review{
	width:100%; 
	height:100%;
	border:dashed 1px;
}
</style>
<script src="../jquery/jquery-1.11.0.min.js"></script>
<script src="../jquery/jquery-ui-1.10.4.min.js"></script>
<script src="../js/utilities_min.js"></script>
<script>
	var blogObject=<?php echo json_encode($blogObject)?>;
	$(document).ready(function(){
		<?php if(isset($blog_id)){?>
		populate_blog();
		<?php }?>
	});
	function upload_photo(){
		$('#uploadphotoform').submit();
		//$('#dialog').dialog("close");
		//$('#dialog2').dialog();
	}
	function display_dialog(dialog_id){
		$('#'+dialog_id).dialog("open");
	}
	function upload_done(){
		var upload_ret = frames['upload_results'].document.getElementsByTagName("body")[0].innerHTML;
		if (upload_ret==''||upload_ret==null) {
			return;
		}else{
			var return_data = upload_ret.split("::");
			if(return_data!=null){
				var return_code = return_data[0];
				var return_message = return_data[1].trim();
				if(return_code==0){
					$('#preview').attr('src',return_message);
					$('#dialog2').dialog('open');
					$('#photo').val('');
					$('#dialog').dialog('close');
				}else{
					alert(return_message);
				}
			}
		}
	}
	function insert_image(textarea_id,img_url){
		//alert(textarea_id);
		var cursorStart = $('#'+textarea_id).prop("selectionStart");
		var cursorEnd = $('#'+textarea_id).prop("selectionEnd");
		if(cursorStart==cursorEnd){
			var cursorPosition = cursorStart;
			var text = $('#'+textarea_id).val();
			var new_text = text.substring(0, cursorPosition)+'\n'+'<img src="'+img_url+'" max-width="500px"/>\n'+text.substring(cursorPosition);
			$('#'+textarea_id).val(new_text);
		}
		
	}
	function insert_bold(textarea_id){
		var cursorStart = $('#'+textarea_id).prop("selectionStart");
		var cursorEnd = $('#'+textarea_id).prop("selectionEnd");
		var text = $('#'+textarea_id).val();
		var new_text = text.substring(0, cursorStart)+'<b>'+text.substring(cursorStart,cursorEnd)+'</b>'+text.substring(cursorEnd);
		$('#'+textarea_id).val(new_text);
	}
	function insert_italics(textarea_id){
		var cursorStart = $('#'+textarea_id).prop("selectionStart");
		var cursorEnd = $('#'+textarea_id).prop("selectionEnd");
		var text = $('#'+textarea_id).val();
		var new_text = text.substring(0, cursorStart)+'<i>'+text.substring(cursorStart,cursorEnd)+'</i>'+text.substring(cursorEnd);
		$('#'+textarea_id).val(new_text);
	}
	$(function(){
		$("#dialog").dialog({
		width: 500,
		height:300,
		autoOpen: false,
		show: {
			effect: "blind",
			duration: 200
		  },
		  hide: {
			effect: "blind",
			duration: 200
		  },
		buttons:[{
			text:"上传",
			click: function(){
				$('#uploadphotoform').submit();
				//$(this).dialog("close");
			}
		}]
		});
		$("#dialog2").dialog({
		width: 'auto',
		height: 'auto',
		autoOpen: false,
		show: {
			effect: "blind",
			duration: 200
		},
		hide: {
			effect: "blind",
			duration: 200
		},
		buttons:[{
			text:"OK",
			click: function(){
				insert_image('content',$('#preview').attr('src'));
				$(this).dialog("close");
			}
		}]
		});
		$("#dialog3").dialog({
		width: 'auto',
		height: 'auto',
		autoOpen: false,
		show: {
			effect: "blind",
			duration: 200
		},
		hide: {
			effect: "blind",
			duration: 200
		},
		buttons:[{
			text:"插入",
			click: function(){
				insert_image('content',$('#img_url').val().trim());
				$(this).dialog("close");
			}
		}]
		});
		
	});
</script>
</head>

<body>

<p><a class="button" target="_blank" href="blogs.php">查看/编辑已发布文章</a></p>
<p><a class="button" target="_blank" href="fix_listing.php">地产功能：修复listing</a></p>
<fieldset style="width:600px; float:left;">
<legend><?php echo $page_title;?></legend>
<p><input id="title" type="text" placeholder="标题"/></p>
<p><input id="source" type="text" placeholder="来源"/></p>
<div class="toolbar">
<a class="button" href="javascript:display_dialog('dialog')">上传图片</a>
<a class="button" href="javascript:display_dialog('dialog3')">插入网络图片</a>
<a class="button" href="javascript:insert_bold('content')">粗体</a>
<a class="button" href="javascript:insert_italics('content')">斜体</a>
</div>
<div id="blog-content"><textarea id="content"></textarea></div>
<div class="toolbar">
<a class="button" id="save_button" href='javascript:save_blog(false)'>储存</a> 
<a class="button" id="publish_button" href='javascript:save_blog(true)'>发布</a>
<a class="button" id="draft_button" href='javascript:draft_blog()' style="display:none;">取消发布</a>
<a class="button" id="delete_button" href="javascript:delete_blog()" style="display:none;">删除</a>
<a class="button" id="exit_button" href="javascript:exit_edit()">退出</a>
<div id="blog_content_html" style="display:none"></div>
</div>

</fieldset>

<div style="float:right; width:500px; height:1000px;">
<div><label for="share">分享链接</label>
<input type="text" id="share" readonly="readonly"/> </div>
<spane>预览</span><iframe id="review" style="width:100%; height:100%;"src=""></iframe> </div>
</body>






<!--dialogs-->
<div id="dialog" title="上传图片">
<form onsubmit="" name="uploadphotoform" id="uploadphotoform" enctype="multipart/form-data" target="upload_results" method="post" action="/server/upload_photo.php">
<span>本地照片: </span><input onblur="" value="" size="48" id="photo" name="photo" type="file">
</form>
<iframe id="upload_results" name="upload_results" onload="upload_done()" src="" style="height: 100px; border: 1px solid rgb(204, 204, 204); visibility: hidden;">Ready!</iframe>

<!--<iframe id="upload_results" name="upload_results" onload="upload_done()" src="" style="height: 10px; border: 1px solid rgb(204, 204, 204); visibility: hidden;">Ready!</iframe>-->
</div>
<div id="dialog2" title="上传成功">
	<img id="preview" src=""/>
</div>
<div id="dialog3" title="插入网络图片">
	<input id="img_url" type="text" />
</div>
 
<script type="text/javascript">
function populate_blog(){
	$('#title').val(blogObject['blog_title']);
	$('#source').val(blogObject['blog_source']);
	$('#content').html(blogObject['blog_content']);
	$('#review').attr('src',"http://home365.ca/json-plugs/article/"+blogObject['blog_id']);
	$('#share').val("http://home365.ca/json-plugs/article/"+blogObject['blog_id']);
	if(blogObject['status']=='P'){
		$('#publish_button').hide();
		$('#draft_button').show();
	}else{
		$('#publish_button').show();
		$('#draft_button').hide();
	}
}
function save_blog(post_blog){
	var blog_title=$('#title').val().trim();
	var blog_source=$('#source').val().trim();
	var blog_content=$('#content').val().trim();
	if(blog_content.length>0&&blog_title.length>0){
		if(blog_source.length>0){
			blogObject['blog_source']=blog_source;
		}
		blogObject['blog_content']=blog_content;
		blogObject['blog_title']=blog_title;
		$('#blog_content_html').html(linkifyYouTubeURLs(blog_content).replace(/\012/g,'<br/>'));
		blogObject['media_url']=$('#blog_content_html img:eq(0)').attr('src');
		blogObject['post_blog']=(post_blog?'Y':'N');
		$.post('../server/save_blog.php',blogObject,function(){
				var return_data = arguments[0];
				var blog_id = return_data['blog_id'];
				alert(return_data.return_message);
				if(blog_id!=''){
					blogObject['blog_id']=blog_id;
					$('#review').attr('src',"http://home365.ca/json-plugs/article/"+blog_id);
					$('#share').val("http://home365.ca/json-plugs/article/"+blog_id);
					if(blogObject['post_blog']=='Y'){
						reset_input();
						alert('输入框已重置，可以输入新文章');
						blogObject['blog_id']='';
						//window.location.assign("http://home365.ca/json-plugs/article/"+blogObject['blog_id']);
					}
					
				}
				
			},'json');
	}else{
		alert('请输入标题和内容');
	}
	
}
function reset_input(){
	$('#title').val('');
	$('#source').val('');
	$('#content').val('');
}
function draft_blog(){
	
}
function delete_blog(){
	alert("deleting blog");
}
function exit_edit(){
	if(confirm("退出编辑？你将丢掉所有未储存内容")){
		window.open('','_self');
		window.close();
	}
}
</script>
</html>