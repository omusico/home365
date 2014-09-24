<?php
//error_reporting(E_ALL);
$document_root='/home/home365/public_html';
$domain_name='http://www.home365.ca/';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$blog_id = isset($_POST['blog_id'])?$_POST['blog_id']:$_GET['blog_id'];
$app = isset($_POST['app'])?$_POST['app']:$_GET['app'];

if(!isset($blog_id)){
	echo "blog_id paramater missing";
}else{
	$selectSQL = "SELECT * FROM blogs WHERE blog_id=".$blog_id.
				" AND removed = 'N'";
	$get_blog = mysql_query_or_die($selectSQL, $useradmin);
	$blogObject=array();
	if($row_get_blog=mysql_fetch_assoc($get_blog)){
		$blogObject=$row_get_blog;
	}
	$blogObject['blog_content'] = preg_replace('/\012/','<br/>',linkifyYouTubeURLs($blogObject['blog_content']));
	$blogObject['blog_content'] = replace_hyperlink($blogObject['blog_content']);
	
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo (isset($blogObject['blog_title'])&&$blogObject['blog_title']!=''?$blogObject['blog_title']:drupal_substr(str_replace('"','\"',preg_replace('/^.+：/','',$clean_cotent)),0,45)); ?></title>
<meta name="description" content="<?php echo drupal_substr(str_replace('"','\"',$clean_cotent),0,200); ?>"/>
<script src="<?php echo $domain_name;?>jquery/jquery-1.11.0.min.js" type="text/javascript"></script>
<link href="<?php echo $domain_name;?>json-plugs/css/ios_main.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo $domain_name;?>fancybox/source/jquery.fancybox.css" type="text/css" media="screen"/>
<script type="text/javascript" src="<?php echo $domain_name;?>fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="<?php echo $domain_name;?>jquery/jquery.cookie.js"></script>
<script type="text/javascript">
	function close_ad(){
		$('#dockingdiv').hide("slow");
		$.cookie('showAd', 0);
		//$.cookie('showAd', 0, { expires: 7 });
	}
	var showAd = $.cookie('showAd');
	$(document).ready(function() {
		if(showAd==0){
			$('#dockingdiv').hide();
		}
		$(".fancybox")
			.attr('rel', 'gallery')
			.fancybox({
				padding : 0
		});
		<?php if(isset($app)){?>
			$('#dockingdiv').hide();
			//close_ad();
		<?php	}?>
	});
	
	/*$(window).scroll(function(){
		var scrollTop = $(window).scrollTop();
		$('#test').val(scrollTop);
		$('#dockingdiv').css('top',scrollTop+'px');
		if(scrollTop%400==0){
			if(!$('#dockingdiv').is(":visible") ){
				$('#dockingdiv').show();
			}
		}
	});
	$(window).trigger('scroll');*/
</script>
</head>

<body>
	

<div><input id="test" type="text" style="position:fixed; display:none;"/></div>
	<div id="dockingdiv" style="width:100%;">

        <div style="text-align:center;"><a href="https://itunes.apple.com/ca/app/chan-zhi-daohomapp-wen-ge/id879209391?mt=8"><img style="width:100%;max-width:500px;" src="http://www.home365.ca/json-plugs/img/banner.jpg"/></a><br/><a href="javascript:close_ad()" style="color:#555;text-decoration:underline;font-size:14px;">知道了，不再显示</a></div>
	</div>
	
	<div class="single_blog_summary">
        
        
        
        
		<div class="single_blog_title"><h1><?php echo $blogObject['blog_title'];?></h1></div> 
		<div class="single_blog_author">
			
			<?php echo $blogObject['blog_source']?>
			<span class="timestamp"><?php echo $blogObject['date_created_YVR'];?></span>

		</div>
		
		<div class="single_blog_contnt"><?php echo $blogObject['blog_content'];?></div>
        
	</div>
    
    
    
</body>
<script type="text/javascript">

</script>
</html>
