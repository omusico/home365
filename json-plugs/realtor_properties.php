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

$realtor_id=isset($_POST['realtor_id'])?$_POST['realtor_id']:$_GET['realtor_id'];
$from=isset($_POST['from'])?$_POST['from']:$_GET['from'];
$size=isset($_POST['size'])?$_POST['size']:$_GET['size'];

$order=" listings.date_updated_local,";
$filter=" AND (listing_realtors.list_realtor_1_id='".$realtor_id."' 
						OR listing_realtors.list_realtor_2_id='".$realtor_id."' 
						OR listing_realtors.list_realtor_3_id='".$realtor_id."')";
$limit = " LIMIT ".(isset($from)?$from:0).", ".(isset($size)?$size:20);

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT listings.sysid, listings.album_id, listings.date_updated_local, address, postal_code, street_name, street_type, unit_number, area, sub_area, sub_area_desc, city, built_year, house_number, list_price, gross_taxes, for_tax_year, strata_maint_fee, DATE(list_date)AS list_date, mls_number, bedrooms, bathrooms, public_remarks, public_remarks_2, lot_size_sqt, lot_size_sqm, floor_area_total, title_to_land, site_influences, type_of_dwelling, listings.open_house, listing_geoaddress.lat, listing_geoaddress.lng, list_realtor_1_id, list_realtor_2_id, list_realtor_3_id FROM listings".
			" LEFT JOIN city_profile ON listings.city=city_profile.en".
			" LEFT JOIN listing_geoaddress ON listings.sysid=listing_geoaddress.sysid".
			" LEFT JOIN listing_realtors ON listings.sysid=listing_realtors.sysid".
			" LEFT JOIN listing_ranking ON listings.sysid=listing_ranking.sysid".
			" WHERE status='A' AND !ISNULL(listings.album_id) AND listings.property_type!='Land Only' AND area_desc!='VOT' AND area_desc!='FOT'".$filter." AND !ISNULL(lat) ORDER BY".$order." listing_ranking.ranking DESC, city_profile.ranking DESC, date_updated_local DESC".$limit;
$get_properties = mysql_query_or_die($selectSQL,$useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
if($size=='all'&&$row[0]>300){
	$return_array = array("return_code"=>0, "error"=>"More than 500 results");
	echo json_encode($return_array);
	exit();
}
$properties_array=array();
while($row_get_properties=mysql_fetch_assoc($get_properties)){
	foreach ($row_get_properties as $key=>$value){
		if($value==null){
			$row_get_properties[$key]='N/A';
		}
	}
	$album=get_album($row_get_properties['sysid']);
	if(is_array($album)){
		$row_get_properties['photo_path']=$album[0];
		$row_get_properties['album_array']=$album;
	}
	if($row_get_properties['photo_path']==null){
		$row_get_properties['photo_path']='N/A';
	}
	if(count($row_get_properties['album_array'])<1){
		$row_get_properties['album_array']='N/A';
	}
	//$row_get_properties['title_to_land']=translate_title_to_land($row_get_properties['title_to_land']);

	if($realtor_1=get_realtor_profile($row_get_properties['list_realtor_1_id'])){
		$row_get_properties['realtor_1_profile']=$realtor_1;
	}
	if($realtor_2=get_realtor_profile($row_get_properties['list_realtor_2_id'])){
		$row_get_properties['realtor_2_profile']=$realtor_2;
	}
	if($realtor_3=get_realtor_profile($row_get_properties['list_realtor_3_id'])){
		$row_get_properties['realtor_3_profile']=$realtor_3;
	}
	$row_get_properties['type_of_dwelling']=translate_property_type($row_get_properties['type_of_dwelling']);
	if($row_get_properties['sub_area_desc']!='VVWDT'){
		$row_get_properties['area']=translate_city_area($row_get_properties['area']);
	}else{
		$row_get_properties['area']="温市中心";
	}
	if($row_get_properties['lot_size_sqt']==0){
		$row_get_properties['lot_size_sqt']='N/A';
	}
	if($row_get_properties['lot_size_sqm']==0){
		$row_get_properties['lot_size_sqm']='N/A';
	}
	
	/*$timestring_updated_local=strtotime($row_get_properties['date_updated_local']);
	$row_get_properties['date_updated_local']=$timestring_updated_local;
	if((time()-(60*60*24)) < $timestring_updated_local){
		$last_24_hr_count++;
	}*/
	array_push($properties_array,$row_get_properties);
}

$return_array=array("sql"=>$selectSQL,"results_found"=>$row[0], "from"=>$from,"properties"=>$properties_array);
echo json_encode($return_array);



//functions
function translate_title_to_land($title){
	if (strpos($title,'Freehold') !== false) {
		if(strpos($title,'Nonstrata')!==false){
			$title.="(完全所有权非共管物业)";
		}elseif(strpos($title,'Strata')!==false){
			$title.="(完全所有权共管物业)";
		}else{
			$title.="(完全所有权物业)";
		}
	}
	if(strpos($title,'Leasehold') !== false){
		$title.="(租赁，仅使用权)";
	}
	return $title;
}
function translate_city_area($area){
	global $useradmin;
	$selectSQL = "SELECT * FROM area_profile WHERE en='$area'";
	$row_get_area = mysql_fetch_assoc(mysql_query_or_die($selectSQL,$useradmin));
	if(!empty($row_get_area)){
		return $row_get_area['cn'];	
	}else{
		return $area;
	}
}
function get_city($keyword){
	global $useradmin;
	$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT en FROM city_profile WHERE en='".$keyword."' OR cn='".$keyword."' OR cn_tr='".$keyword."'";
	$get_city = mysql_query_or_die($selectSQL, $useradmin);
	$row_city=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	$city_array=array();
	while($row_get_city=mysql_fetch_assoc($get_city)){
		array_push($city_array, $row_get_city);
	}
	return $city_array;
}
function get_sub_area($keyword){
	global $useradmin;
	$selectSQL = "SELECT DISTINCT sub_area FROM subarea_profile WHERE sub_area='".$keyword."'";
	$get_sub_area=mysql_query_or_die($selectSQL, $useradmin);
	$sub_area_array=array();
	while ($row_get_sub_area=mysql_fetch_assoc($get_sub_area)){
		array_push($sub_area_array,$row_get_sub_area);
	}
	return $sub_area_array;
}
function translate_property_type($type){
	switch($type){
		case 'House/Single Family':
		$type='独立屋';
		break;
		case 'Apartment/Condo':
		$type='公寓';
		break;
		case 'Townhouse':
		$type='联排别墅';
		break;
		case 'House with Acreage':
		$type='庄园别墅';
		break;
		case 'Manufactured':
		$type='移动式住房';
		break;
		case '1/2 Duplex':
		$type='双拼屋';
		break;
	}
	return $type;
}

function get_album($sysid){
	global $useradmin;
	$selectAlbumSQL = "SELECT * FROM listing_album".
					" LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id".
					" LEFT JOIN photo_profile ON photo_profile.photo_id=album_profile.photo_id AND !ISNULL(photo_path)".
					" WHERE listing_album.sysid=".$sysid.
					" GROUP BY photo_profile.photo_path";
	$get_album=mysql_query_or_die($selectAlbumSQL, $useradmin);
	$photo_array=array();
	while ($row_get_album=mysql_fetch_assoc($get_album)){
		array_push($photo_array, $row_get_album['photo_path']);
	}
	//var_dump($photo_array);
	
	if($photo_array==null){
		return "N/A";
	}else{
		return $photo_array;
	}
		
}
function get_realtor_profile($realtor_id){
	global $useradmin;
	$selectSQL = "SELECT * FROM realtor_profile WHERE realtor_id='".$realtor_id."'";
	$get_realtor = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_realtor=mysql_fetch_assoc($get_realtor)){
		foreach ($row_get_realtor as $key=>$value){
			if($value==null){
				$row_get_realtor[$key]='N/A';
			}
		}
		return $row_get_realtor;
	}else{
		return false;
	}
}
function get_firm_profile($firm_code){
	global $useradmin;
	$selectSQL = "SELECT * FROM firm_profile WHERE firm_code='".$firm_code."'";
	$get_firm = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_firm=mysql_fetch_assoc($get_firm)){
		foreach ($row_get_firm as $key=>$value){
			if($value==null){
				$row_get_firm[$key]='N/A';
			}
		}
		return $row_get_firm;
	}else{
		return false;
	}
}


?>