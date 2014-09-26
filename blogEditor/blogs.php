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
$selectSQL = "SELECT * FROM blogs ORDER BY date_updated_YVR DESC LIMIT 0, 20";
$get_blogs= mysql_query_or_die($selectSQL, $useradmin);
$blogs_html='<table border="1px" cellspacing="0px" cellpadding="5" style="border-collapse:collapse">';
$blogs_html.='<tr>';
$blogs_html.='<td>标题</td>';
$blogs_html.='<td>最后更新</td>';
$blogs_html.='<td>共享链接</td>';
$blogs_html.='<td>操作</td>';
$blogs_html.='</tr>';
while ($row_get_blogs=mysql_fetch_assoc($get_blogs)){
	$blogs_html.='<tr>';
	$blogs_html.='<td>'.$row_get_blogs['blog_title'].'</td>';
	$blogs_html.='<td>'.$row_get_blogs['date_updated_YVR'].'</td>';
	$blogs_html.='<td>http://home365.ca/json-plugs/article/'.$row_get_blogs['blog_id'].'</td>';
	$blogs_html.='<td><a href="http://home365.ca/blogEditor/index.php?blog_id='.$row_get_blogs['blog_id'].'"><span style="color:red;">点击编辑</span></a></td>';
	$blogs_html.='</tr>';
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php echo $blogs_html;?>
</body>
</html>
