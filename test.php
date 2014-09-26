<?php
require_once 'dbconnect/dbconnect.php';
require_once 'utilities/utilities.php';
require_once 'RETS.php';
if(mysql_select_db("home365_ios",$useradmin)){
	
}else{
	echo "Error selecting database, exited.";
	exit();
}

$rets = new RETS();
$rets->url='http://brc.rebretstest.interealty.com/Login.asmx/Login';
$rets->user='RETSALLISONJ';
$rets->password='RE@LE$7AT3';
$rets->useragent='RETSAllisonJiang/1.0';
$rets->useragent_password='8tHIMi7aL#e4Utd';

$response=$rets->Login();
var_dump($response);
$listings_array=$rets->GetDataArray('Property','11','(363=|A)', null, null);
foreach($listings_array as $key=>$value){
	$selectSQL = "SELECT * FROM listings WHERE sysid=".$value['sysid'];
	
	$get_listing = mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_listing=mysql_fetch_assoc($get_listing)){
		echo "Record found in database\n";
		$updateSQL = sprintf("UPDATE listings SET property_type=%s, address=%s, postal_code=%s, unit_number=%s, city=%s, province=%s, built_year=%s, house_number=%s, list_price=%s, list_date=%s, mls_number=%s, bedrooms=%s, bathrooms=%s, public_remarks=%s, public_remarks_2=%s, lot_size_sqt=%s, lot_size_sqm=%s, floor_area_total=%s, site_influences=%s, type_of_dwelling=%s,publish_on_internet=%s, status=%s WHERE sysid=".$value['sysid'],
		
		GetSQLValueString($value['1'],"text"),
		GetSQLValueString($value['14'],"text"),
		GetSQLValueString($value['11'],"text"),
		GetSQLValueString($value['2971'],"int"),
		GetSQLValueString($value['3794'],"text"),
		GetSQLValueString($value['88'],"text"),
		GetSQLValueString($value['16'],"int"),
		GetSQLValueString($value['181'],"int"),
		GetSQLValueString($value['226'],"double"),
		GetSQLValueString($value['224'],"date"),
		GetSQLValueString($value['248'],"text"),
		GetSQLValueString($value['378'],"int"),
		GetSQLValueString($value['3928'],"int"),
		GetSQLValueString($value['411'],"text"),
		GetSQLValueString($value['3985'],"text"),
		GetSQLValueString($value['2457'],"double"),
		GetSQLValueString($value['2460'],"double"),
		GetSQLValueString($value['3922'],"double"),
		GetSQLValueString($value['3926'],"text"),
		GetSQLValueString($value['2733'],"text"),
		GetSQLValueString(($value['3']=='Yes'?'Y':'N'),"text"),
		GetSQLValueString(($value['363']=='Active'?'A':'D'),"text"),
		GetSQLValueString($value['sysid'],"int")
		);
		$result=mysql_query_or_die($updateSQL,$useradmin);
		$album_id=get_album_id($value['sysid']);
		if(!$album_id){
			$album_id=create_album($value['sysid']);
		}
		
		}else{
		$response=$rets->GetPhoto('Property',$value['sysid'].':*', 'images');
		$photo_array=$response['photo_array'];
		$address=$value['14'].' '.$value['3794'].' '.$value['88'];
		get_geocode($address,$value['sysid']);
		echo "Creating new record\n";
			$insertSQL = sprintf("INSERT INTO listings (sysid, property_type, address, postal_code, unit_number, city, province, built_year, house_number, list_price, list_date, mls_number, bedrooms, bathrooms, public_remarks, public_remarks_2, lot_size_sqt, lot_size_sqm, floor_area_total, site_influences, type_of_dwelling,publish_on_internet,status) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
		GetSQLValueString($value['sysid'],"int"),
		GetSQLValueString($value['1'],"text"),
		GetSQLValueString($value['14'],"text"),
		GetSQLValueString($value['11'],"text"),
		GetSQLValueString($value['2971'],"int"),
		GetSQLValueString($value['3794'],"text"),
		GetSQLValueString($value['88'],"text"),
		GetSQLValueString($value['16'],"int"),
		GetSQLValueString($value['181'],"int"),
		GetSQLValueString($value['226'],"double"),
		GetSQLValueString($value['224'],"date"),
		GetSQLValueString($value['248'],"text"),
		GetSQLValueString($value['378'],"int"),
		GetSQLValueString($value['3928'],"int"),
		GetSQLValueString($value['411'],"text"),
		GetSQLValueString($value['3985'],"text"),
		GetSQLValueString($value['2457'],"double"),
		GetSQLValueString($value['2460'],"double"),
		GetSQLValueString($value['3922'],"double"),
		GetSQLValueString($value['3926'],"text"),
		GetSQLValueString($value['2733'],"text"),
		GetSQLValueString(($value['3']=='Yes'?'Y':'N'),"text"),
		GetSQLValueString(($value['363']=='Active'?'A':'D'),"text")
		);
		$result=mysql_query_or_die($insertSQL,$useradmin);
		$album_id=create_album($value['sysid']);
		
		for($i=0;$i<count($photo_array);$i++){
			$insertPhotoSQL = sprintf("INSERT INTO photo_profile(photo_path)VALUES(%s)",
								GetSQLValueString($photo_array[$i],"text"));
			$result=mysql_query_or_die($insertPhotoSQL,$useradmin);
			$photo_id=mysql_insert_id($useradmin);
			$insertAlbumSQL = sprintf("INSERT INTO album_profile(album_id, photo_id, cover)VALUES(%s,%s,%s)",
								GetSQLValueString($album_id,"int"),
								GetSQLValueString($photo_id,"int"),
								GetSQLValueString($i==0?'Y':'N',"text"));
			$result=mysql_query_or_die($insertAlbumSQL,$useradmin);
		}
		
		//insert record to listing_realtors
		$insertSQL = sprintf("INSERT INTO listing_realtors(sysid, list_realtor_1_id, list_realtor_2_id, list_realtor_3_id) VALUES(%s,%s,%s,%s)",
		GetSQLValueString($value['sysid'],"int"),
		GetSQLValueString($value['342'],"text"),//list_realtor_1_id
		GetSQLValueString($value['2697'],"text"),//list_realtor_2_id
		GetSQLValueString($value['2699'],"text"));//list_realtor_3_id
		$result=mysql_query_or_die($insertSQL,$useradmin);
		check_insert_realtor($value['342'],$value['2703'],$value['2711'],$value['2685']);
		check_insert_realtor($value['2697'],$value['2327'],$value['2713'],$value['2707']);
		check_insert_realtor($value['2699'],$value['2329'],$value['2717'],$value['2701']);
	
	
		//insert record to listing_firms table
		$insertSQL=sprintf("INSERT INTO listing_firms (sysid, list_firm_1_code, list_firm_1_name, list_firm_1_phone, list_firm_1_fax, list_firm_1_url, list_firm_2_code, list_firm_2_name, list_firm_2_phone, list_firm_2_fax, list_firm_2_url) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
		GetSQLValueString($value['sysid'],"int"),
		GetSQLValueString($value['222'],"text"),//firm 1 code
		GetSQLValueString($value['2679'],"text"),//firm 1 name
		GetSQLValueString(str_replace('-','',$value['2681']),"text"),//firm 1 phone
		GetSQLValueString(str_replace('-','',$value['2675']),"text"),//firm 1 fax
		GetSQLValueString($value['2685'],"text"),//firm 1 url
		GetSQLValueString($value['2689'],"text"),//firm 2 code
		GetSQLValueString($value['2325'],"text"),//firm 2 name
		GetSQLValueString(str_replace('-','',$value['2683']),"text"),//firm 2 phone
		GetSQLValueString(str_replace('-','',$value['2677']),"text"),//firm 2 fax
		GetSQLValueString($value['2687'],"text")//firm 2 name
		);
		$result=mysql_query_or_die($insertSQL,$useradmin);
		
	}
}
function check_insert_realtor($realtor_id, $name, $phone, $url){
	global $useradmin;
	if(!empty($realtor_id)){
		$selectSQL = "SELECT * FROM realtor_profile WHERE realtor_id='".$realtor_id."'";
		$get_realtor = mysql_query_or_die($selectSQL, $useradmin);
		if($row_get_realtor=mysql_fetch_assoc($get_realtor)){
		}else{
			$insertSQL = sprintf("INSERT INTO realtor_profile (realtor_id, name, phone, url) VALUES (%s, %s, %s, %s)",
			GetSQLValueString($realtor_id,"text"),
			GetSQLValueString($name,"text"),
			GetSQLValueString(str_replace('-','',$phone),"text"),
			GetSQLValueString($url,"text"));
			$result = mysql_query_or_die($insertSQL,$useradmin);
		}
	}
	
}
function get_album_id($sysid){
	global $useradmin;
	$selectSQL = "SELECT * FROM listing_album WHERE sysid=".$sysid;
	$get_album = mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_album=mysql_fetch_assoc($get_album)){
		return $row_get_album['album_id'];
	}else{
		return false;
	}
}
function create_album($sysid){
	global $useradmin;
	$insertSQL=sprintf("INSERT INTO listing_album (sysid) VALUES(%s)",
						GetSQLValueString($sysid,"int"));
	$result=mysql_query_or_die($insertSQL,$useradmin);
	$album_id=mysql_insert_id($useradmin);
	return $album_id;
}

function get_geocode($address, $sysid){
	global $useradmin;
	$selectSQL = "SELECT * FROM listing_geoaddress WHERE sysid=".$sysid;
	$get_geocode=mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_geocode=mysql_fetch_assoc($get_geocode)){
		return $row_get_geocode;
	}else{
		$url="http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($address);
		$resp_json = file_get_contents($url);
		$resp = json_decode($resp_json, true);
		if($resp['status']='OK'){
			$insertSQL = sprintf("INSERT INTO listing_geoaddress(sysid, lat, lng)VALUES(%s,%s,%s)",
						GetSQLValueString($sysid,"int"),
						GetSQLValueString($resp['results'][0]['geometry']['location']['lat'],"double"),
						GetSQLValueString($resp['results'][0]['geometry']['location']['lng'],"double"));
			$result=mysql_query_or_die($insertSQL,$useradmin);
			return $resp['results'][0]['geometry']['location'];
		}else{
			return false;
		}
	}
}

?>