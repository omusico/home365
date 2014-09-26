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
$rets->url='http://brc.retsca.interealty.com/Login.asmx/Login';
$rets->user='RETSALLISONJ';
$rets->password='RE@LE$7AT3';
$rets->useragent='RETSAllisonJiang/1.0';
$rets->useragent_password='8tHIMi7aL#e4Utd';
$current_GMT_time=get_GMT(time());
$current_GMT_time_string=date("Y-m-d H:i:s",$current_GMT_time);
$current_YVR_time=getTimeByCity($current_GMT_time,'YVR');
$current_YVR_time_string=date("Y-m-d H:i:s",$current_YVR_time);

//The following code is to test if the cron job is executed.
$file="file.txt";
$current=file_get_contents($file);
$current.="$current_YVR_time_string\n";
file_put_contents($file,$current);

//login and receive server response.
$response=$rets->Login();
var_dump($response);


//$response=$rets->GetCount('Property','11','(363=|A)');
//print $response.'<br/>';

$sysid_array=$rets->GetDataArray('Property','11','(363=|A)', 'sysid', null);
//echo count($sysid_array).'<br/>';

foreach($sysid_array as $index=>$array){
	//echo '<br/>['.$index.']'.$array['sysid'].'<br/>';
	insert_sysid($array['sysid']);
}

$selectSQL = "SELECT sysid FROM sysid_raw WHERE imported='N' AND problem='N' LIMIT 0, 50";
$get_sysid=mysql_query_or_die($selectSQL,$useradmin);
while ($row_get_sysid=mysql_fetch_assoc($get_sysid)){
	$new_sysid=$row_get_sysid['sysid'];
	$listing_array = $rets->GetDataArray('Property','11','(sysid='.$new_sysid.')',null,null);
	if(insert_update_listing($listing_array[0])){
		if(update_sysid_in_raw($new_sysid)){
			echo "<br/><span style=\"color:#336633;\">[info] sysid updated in sysid_raw database. $new_sysid imported='Y'.</span><br/>";
		}
	}else{
		echo "<br/><span style=\"color:#FF0000;\">[warning] didn't get anything from the server</span><br/>";
		sysid_problem_in_raw($new_sysid);
	}
}
/*$selectSQL = "SELECT sysid FROM listings WHERE ISNULL(title_to_land) LIMIT 0,8";
$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
while ($row_get_sysid=mysql_fetch_assoc($get_sysid)){
	$new_sysid=$row_get_sysid['sysid'];
	$listing_array = $rets->GetDataArray('Property','11','(sysid='.$new_sysid.')',null,null);
	if(insert_update_listing($listing_array[0])){
		echo "<br/><span style=\"color:#336633\">[info] Title to land updated</span><br/>";
	}
}*/


#===============FUNCTIONS BELOW===============#

function insert_sysid($sysid){
	global $useradmin;
	if(!check_sysid_in_raw($sysid)){
		$insertSQL = sprintf("INSERT INTO sysid_raw (sysid, imported) VALUES (%s, %s)",
			GetSQLValueString($sysid,"int"),
			GetSQLValueString(check_sysid_in_listings($sysid),"text"));
		$result=mysql_query_or_die($insertSQL, $useradmin);
		echo "<br/><span style=\"color:#336633;\">[info]new sysid inserted to sysid_raw database.</span><br/>";
	}else{
	}
}

function insert_update_listing($value){
	global $useradmin,$rets,$current_YVR_time_string;
	if(!empty($value)){
		$selectSQL = "SELECT * FROM listings WHERE sysid=".$value['sysid'];
		$get_listing = mysql_query_or_die($selectSQL,$useradmin);
		if($row_get_listing =mysql_fetch_assoc($get_listing)){
		echo "<span style=\"color:#336633\">Record found in database</span>\n";
		$updateSQL = sprintf("UPDATE listings SET property_type=%s, address=%s, postal_code=%s, unit_number=%s, area_desc=%s, area=%s, sub_area_desc=%s, sub_area=%s, city=%s, province=%s, built_year=%s, house_number=%s, list_price=%s, gross_taxes=%s, for_tax_year=%s, list_date=%s, mls_number=%s, bedrooms=%s, bathrooms=%s, public_remarks=%s, public_remarks_2=%s, lot_size_sqt=%s, lot_size_sqm=%s, floor_area_total=%s, site_influences=%s, type_of_dwelling=%s, title_to_land=%s, publish_on_internet=%s, status=%s, last_trans_date=%s, date_updated_local=%s WHERE sysid=".$value['sysid'],
		GetSQLValueString($value['1'],"text"),
		GetSQLValueString($value['14'],"text"),
		GetSQLValueString($value['11'],"text"),
		GetSQLValueString($value['2971'],"int"),
		GetSQLValueString($value['2233'],"text"),//area desc
		GetSQLValueString($value['2283'],"text"),//area
		GetSQLValueString($value['2570'],"text"),//sub_area_desc
		GetSQLValueString($value['2568'],"text"),//sub_area
		GetSQLValueString($value['3794'],"text"),
		GetSQLValueString($value['88'],"text"),
		GetSQLValueString($value['16'],"int"),
		GetSQLValueString($value['181'],"int"),
		GetSQLValueString($value['226'],"double"),
		GetSQLValueString($value['2673'],"double"),
		GetSQLValueString($value['2651'],"int"),
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
		GetSQLValueString($value['2733'],"text"),//type of dwelling
		GetSQLValueString($value['2737'],"text"),//title to land
		GetSQLValueString(($value['3']=='Yes'?'Y':'N'),"text"),
		GetSQLValueString(($value['363']=='Active'?'A':'D'),"text"),
		GetSQLValueString($value['217'],"date"),
		GetSQLValueString($current_YVR_time_string,"date"),
		GetSQLValueString($value['sysid'],"int"));
		$result=mysql_query_or_die($updateSQL,$useradmin);
		$album_id=get_album_id($value['sysid']);
		if(!$album_id){
			$album_id=create_album($value['sysid']);
		}
		check_insert_firm($value['222'],$value['2679'],$value['2681'],$value['2675'],$value['2685']);
		check_insert_firm($value['2689'],$value['2325'],$value['2683'],$value['2677'],$value['2687']);	
	}else{
		//$response=$rets->GetPhoto('Property',$value['sysid'].':*', 'images');
		//$photo_array=$response['photo_array'];
		$address=$value['14'].' '.$value['3794'].' '.$value['88'];
		if(!get_geocode($address,$value['sysid'])){
			echo '<br/><span style="color:#ff0000;">[Error] No geo code found! Address:'.urlencode($address).' sysid: '.$value['sysid'].'</span><br/>';
		}
		echo "<span style=\"color:#336633;\">Creating new record</span>\n";
		$insertSQL = sprintf("INSERT INTO listings (sysid, property_type, address, postal_code, unit_number, area_desc, area, sub_area_desc, sub_area, city, province, built_year, house_number, list_price, gross_taxes, for_tax_year, list_date, mls_number, bedrooms, bathrooms, public_remarks, public_remarks_2, lot_size_sqt, lot_size_sqm, floor_area_total, site_influences, type_of_dwelling, title_to_land, publish_on_internet,status, date_imported_local, date_updated_local, last_trans_date) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
		GetSQLValueString($value['sysid'],"int"),
		GetSQLValueString($value['1'],"text"),//property type
		GetSQLValueString($value['14'],"text"),//address
		GetSQLValueString($value['11'],"text"),//postal code
		GetSQLValueString($value['2971'],"int"),//unit number
		GetSQLValueString($value['2233'],"text"),//area desc
		GetSQLValueString($value['2283'],"text"),//area
		GetSQLValueString($value['2570'],"text"),//sub_area_desc
		GetSQLValueString($value['2568'],"text"),//sub_area
		GetSQLValueString($value['3794'],"text"),//city
		GetSQLValueString($value['88'],"text"),//province
		GetSQLValueString($value['16'],"int"),//built year
		GetSQLValueString($value['181'],"int"),//house number
		GetSQLValueString($value['226'],"double"),//list price
		GetSQLValueString($value['2673'],"double"),//gross taxes
		GetSQLValueString($value['2651'],"int"),//for tax year
		GetSQLValueString($value['224'],"date"),//list date
		GetSQLValueString($value['248'],"text"),//mls number
		GetSQLValueString($value['378'],"int"),//total bedrooms
		GetSQLValueString($value['3928'],"int"),//total bathrooms
		GetSQLValueString($value['411'],"text"),//public remarks
		GetSQLValueString($value['3985'],"text"),//public remarks 2
		GetSQLValueString($value['2457'],"double"),//lot size square ft
		GetSQLValueString($value['2460'],"double"),//lot size square mt
		GetSQLValueString($value['3922'],"double"),//floor area total
		GetSQLValueString($value['3926'],"text"),//site influence
		GetSQLValueString($value['2733'],"text"),//type of dwelling
		GetSQLValueString($value['2737'],"text"),//title to land
		GetSQLValueString(($value['3']=='Yes'?'Y':'N'),"text"),//publish listing on internet
		GetSQLValueString(($value['363']=='Active'?'A':'D'),"text"),//status
		GetSQLValueString($current_YVR_time_string,"date"),//date_imported
		GetSQLValueString($current_YVR_time_string,"date"),//date_updated
		GetSQLValueString($value['217'],"date")//last trans date
		);
		$result=mysql_query_or_die($insertSQL,$useradmin);
		//$album_id=create_album($value['sysid']);
		/*for($i=0;$i<count($photo_array);$i++){
			$insertPhotoSQL = sprintf("INSERT INTO photo_profile(photo_path)VALUES(%s)",
								GetSQLValueString($photo_array[$i],"text"));
			$result=mysql_query_or_die($insertPhotoSQL,$useradmin);
			$photo_id=mysql_insert_id($useradmin);
			$insertAlbumSQL = sprintf("INSERT INTO album_profile(album_id, photo_id, cover)VALUES(%s,%s,%s)",
								GetSQLValueString($album_id,"int"),
								GetSQLValueString($photo_id,"int"),
								GetSQLValueString($i==0?'Y':'N',"text"));
			$result=mysql_query_or_die($insertAlbumSQL,$useradmin);
		}*/
			
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
			GetSQLValueString($value['2687'],"text")//firm 2 url
		);
		$result=mysql_query_or_die($insertSQL,$useradmin);
		check_insert_firm($value['222'],$value['2679'],$value['2681'],$value['2675'],$value['2685']);
		check_insert_firm($value['2689'],$value['2325'],$value['2683'],$value['2677'],$value['2687']);
	}
	return true;
	}else{
		return false;
	}
}

function update_sysid_in_raw($sysid){
	global $useradmin;
	if(!check_sysid_in_raw($sysid)){
		return false;
	}else{
		$updateSQL = sprintf("UPDATE sysid_raw SET imported =%s WHERE sysid=%s",
			GetSQLValueString('Y',"text"),
			GetSQLValueString($sysid,"int"));
		$result=mysql_query_or_die($updateSQL,$useradmin);
		return true;
	}
}

function sysid_problem_in_raw($sysid){
	global $useradmin;
	$selectSQL = "SELECT sysid FROM sysid_raw WHERE sysid=".$sysid;
	$get_sysid=mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		$updateSQL = sprintf("UPDATE sysid_raw SET problem=%s WHERE sysid=%s",
		GetSQLValueString("Y","text"),
		GetSQLValueString($sysid,"int"));
		$result=mysql_query_or_die($updateSQL,$useradmin);
		echo "<br/><span style=\"color:#336633;\">[info] updated sysid $sysid problem to 'Y' in sysid_raw database.</span><br/>";
	}else{
		echo "<br/><span style=\"color:#FF0000;\">[Error] NO sysid found when updating $sysid problem to 'Y'</span><br/>";
	}
}

function check_sysid_in_raw($sysid){
	global $useradmin;
	$selectSQL = "SELECT sysid FROM sysid_raw WHERE sysid=$sysid";
	$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		return true;
	}else{
		return false;
	}
}

function check_sysid_in_listings($sysid){
	global $useradmin;
	$selectSQL = "SELECT sysid FROM listings WHERE sysid =".$sysid;
	$get_sysid=mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		return 'Y';
	}else{
		return 'N';
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

function check_insert_firm($firm_code, $name, $phone, $fax, $url){
	global $useradmin;
	if(!empty($firm_code)){
		$selectSQL = "SELECT * FROM firm_profile WHERE firm_code='".$firm_code."'";
		$get_firm = mysql_query_or_die($selectSQL, $useradmin);
		if($row_get_firm=mysql_fetch_assoc($get_firm)){
			$updateSQL = sprintf("UPDATE firm_profile SET name=%s, phone=%s, fax=%s, url=%s WHERE firm_code=%s",
			GetSQLValueString($name,"text"),
			GetSQLValueString(str_replace('-','',$phone),"text"),
			GetSQLValueString(str_replace('-','',$fax),"text"),
			GetSQLValueString($url,"text"),
			GetSQLValueString($firm_code,"text"));
			$result=mysql_query_or_die($updateSQL, $useradmin);
		}else{
			$insertSQL = sprintf("INSERT INTO firm_profile (firm_code, name, phone, fax, url) VALUES (%s,%s,%s,%s,%s)",
			GetSQLValueString($firm_code,"text"),
			GetSQLValueString($name,"text"),
			GetSQLValueString(str_replace('-','',$phone),"text"),
			GetSQLValueString(str_replace('-','',$fax),"text"),
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
		echo "<br/>Entering google api<br/>";
		$url="http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($address);
		$resp_json = file_get_contents($url);
		$resp = json_decode($resp_json, true);
		var_dump($resp);
		if($resp['status']=='OK'){
			$lat=$resp['results'][0]['geometry']['location']['lat'];
			$lng=$resp['results'][0]['geometry']['location']['lng'];
			echo '<br/>'.$lat.'<br/>';
			echo '<br/>'.$lng.'<br/>';
			if(!empty($lat)&&!empty($lng)){
				$insertSQL = sprintf("INSERT INTO listing_geoaddress(sysid, lat, lng)VALUES(%s,%s,%s)",
							GetSQLValueString($sysid,"int"),
							GetSQLValueString($lat,"double"),
							GetSQLValueString($lng,"double"));
				$result=mysql_query_or_die($insertSQL,$useradmin);
				return $resp['results'][0]['geometry']['location'];
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
}

?>