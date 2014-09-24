<?php 
/*
* This code lists all the realtors from database table realtor_profile
*/
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$from=isset($_POST['from'])?$_POST['from']:$_GET['from'];


$selectSQL = "SELECT SQL_CALC_FOUND_ROWS * FROM top_banners ORDER BY cover_page ASC LIMIT ".($from?$from:0).", 20";
$get_banners=mysql_query_or_die($selectSQL, $useradmin);
$row_banners=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$banner_array=array();
while($row_get_banners=mysql_fetch_assoc($get_banners)){
	foreach($row_get_banners as $key=>$value){
		if($value==null){
			$row_get_banners[$key]='N/A';
		}
	}
	array_push($banner_array, $row_get_banners);
	
}



$return_array = array("results_found"=>$row_banners[0],"banner_list"=>$banner_array);
echo json_encode($return_array);
$active_index=0;
for($i=0;$i<count($banner_array);$i++){
	if($banner_array[$i]['cover_page']=='Y'){
		$active_index=$i;
	}
}
if($active_index>=(count($banner_array)-1)){
	$active_index=0;
}else{
	$active_index++;
}
set_active($banner_array[$active_index]['banner_id']);
function set_active($index){
		global $useradmin;
		$updateSQL = "UPDATE top_banners SET cover_page = 'N'";
		$result = mysql_query_or_die($updateSQL, $useradmin);
		$updateSQL = "UPDATE top_banners SET cover_page = 'Y' WHERE banner_id = ".$index;
		$result = mysql_query_or_die($updateSQL, $useradmin);
	}
?>
