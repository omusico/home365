<?php 
/*
* This code lists all the realtors from database table realtor_profile
*/
error_reporting(E_ALL);
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$from=isset($_POST['from'])?$_POST['from']:$_GET['from'];


$selectSQL = "SELECT SQL_CALC_FOUND_ROWS * FROM realtor_profile ORDER BY realtor_profile.google_id DESC, RAND() LIMIT ".($from?$from:0).", 20";
$get_realtors=mysql_query_or_die($selectSQL, $useradmin);
$row_realtors=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$realtor_array=array();
while($row_get_realtors=mysql_fetch_assoc($get_realtors)){
	foreach($row_get_realtors as $key=>$value){
		if($value==null){
			$row_get_realtors[$key]='N/A';
		}
	}
	$row_get_realtors['num_of_properties']=find_properties($row_get_realtors['realtor_id']);
	array_push($realtor_array, $row_get_realtors);
	//$realtor_array['num_of_properties']=find_properties($row_get_realtors['realtor_id']);
	
}
$return_array = array("results_found"=>$row_realtors[0],"realtor_list"=>$realtor_array);
echo json_encode($return_array);



function find_properties($realtor_id){
	//echo "<br/>Entered find_properties function<br/>";
	global $useradmin;
	//$order=" listings.date_updated_local,";
	$filter=" AND (listing_realtors.list_realtor_1_id='".$realtor_id."' 
						OR listing_realtors.list_realtor_2_id='".$realtor_id."' 
						OR listing_realtors.list_realtor_3_id='".$realtor_id."')";
	$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT listings.sysid FROM listings".
			" LEFT JOIN listing_realtors ON listings.sysid=listing_realtors.sysid".
			" WHERE status='A' AND !ISNULL(listings.album_id) AND listings.property_type!='Land Only' AND area_desc!='VOT' AND area_desc!='FOT'".$filter." ORDER BY date_updated_local DESC";
	//echo "<br/>".$selectSQL."<br/>";
	$get_properties = mysql_query_or_die($selectSQL,$useradmin);
	$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	//echo "<br/>found ".$row[0]." records<br/>";
	if(is_array($row)){
		return $row[0];
	}else{
		return 0;
	}
}
?>
