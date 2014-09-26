<?php 
//echo $_SERVER['DOCUMENT_ROOT'];
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

require_once '../../test1/server/get_properties_body.php';
$from=isset($from)?$from:0;
$size=isset($size)?$size:12;
$properties_array=get_properties_summary($from, $size);
//var_dump($properties_array);
?>
