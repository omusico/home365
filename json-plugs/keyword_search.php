<?php 

$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

$keyword=isset($_POST['keyword'])?$_POST['keyword']:$_GET['keyword'];

//$selectSQL = "SELECT * FROM blogs WHERE removed='N' AND status= 'P' ORDER BY last_updated_UTC DESC, date_created_UTC DESC";
$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT en, cn FROM city_profile WHERE en LIKE '%".$keyword."%' OR cn LIKE '%".$keyword."%' OR cn_tr LIKE '%".$keyword."%'";
$get_city = mysql_query_or_die($selectSQL, $useradmin);
$row_city=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$city_array=array();
while($row_get_city=mysql_fetch_assoc($get_city)){
	array_push($city_array, $row_get_city);
}

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT address, city FROM listings WHERE address LIKE '%".$keyword."%' AND status='A' AND property_type!='Land Only' AND !ISNULL(listings.album_id) AND area_desc!='VOT' AND area_desc!='FOT'";
$get_address=mysql_query_or_die($selectSQL,$useradmin);
$row_address=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$address_array=array();
while($row_get_address=mysql_fetch_assoc($get_address)){
	array_push($address_array,$row_get_address);
}
$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sub_area FROM subarea_profile WHERE sub_area LIKE '%".$keyword."%'";
$get_sub_area=mysql_query_or_die($selectSQL, $useradmin);
$subarea_rows=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$subarea_array=array();
while($row_get_sub_area=mysql_fetch_assoc($get_sub_area)){
	array_push($subarea_array, $row_get_sub_area);
}

if(count($city_array)>0||count($address_array)>0||count($subarea_array)>0){
	$return_code=0;
	$error="success";
}else{
	$return_code=1;
	$error="no results found";
}
$return_array = array("return_code"=>$return_code,"error"=>$error,"city_number"=>$row_city[0], "city"=>$city_array, "address_number"=>$row_address[0], "address"=>$address_array,"sub_area_number"=>$subarea_rows[0],"sub_area"=>$subarea_array);
echo json_encode($return_array);
?>
