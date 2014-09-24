<?php 

$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$from=isset($_POST['from'])?$_POST['from']:$_GET['from'];
if(empty($from)){
	$from=0;
}
$size=20;
$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT * FROM blogs WHERE removed='N' AND status= 'P' ORDER BY date_updated_UTC DESC, date_created_UTC DESC LIMIT $from, $size";

$get_blogs=mysql_query_or_die($selectSQL,$useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$blogs_array=array();
while($row_get_blogs=mysql_fetch_assoc($get_blogs)){
	foreach($row_get_blogs as $key=>$value){
		if($value==null){
			$row_get_blogs[$key]='';
		}
		$row_get_blogs['article_url']="http://www.home365.ca/json-plugs/article/".$row_get_blogs['blog_id']."/t1";
		$row_get_blogs['share_url']="http://www.home365.ca/json-plugs/article/".$row_get_blogs['blog_id'];
		
	}
	array_push($blogs_array,$row_get_blogs);
}

$return_array=array("results_found"=>$row[0],"from"=>$from, "blogs"=>$blogs_array);
echo json_encode($return_array);
?>
