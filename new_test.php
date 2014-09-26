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
$current_YVR_date=date("Y-m-d",$current_YVR_time);
$property_type = 7;

//The following code is to test if the cron job is executed.
$file="file.txt";
$current=file_get_contents($file);
$current.="$current_YVR_time_string from new test.php\n";
file_put_contents($file,$current);


//get and update open house data
get_open_house();


/*
$response=$rets->GetDataArray('Property',$property_type,'(363=|A)',null,1);
$listing_array = $response[0];
foreach($listing_array as $key=>$value){
	echo '['.$key.']'.get_column_name($key).':'.$value.'<br/>';
}*/

//first patch all properties whose class=1(residential detached),2(residential attached) or 7(multifamily). These properties have data such as strata_maint_fee that can't be accessed when importing these sysid's

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sysid, property_type FROM listings WHERE ISNULL(strata_maint_fee) AND status='A' AND patched='N' AND property_type!='Land Only' LIMIT 0, 50";
$get_sysid=mysql_query_or_die($selectSQL, $useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
if($row[0]>0){
	echo '<br/>There are '.$row[0].'listings to be patched<br/>';
	//login and receive server response.
	$response=$rets->Login();
	var_dump($response);
	while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		$new_sysid=$row_get_sysid['sysid'];
		switch($row_get_sysid['property_type']){
			case 'Multifamily': $property_type=7; break;
			case 'Residential Detached': $property_type=1; break;
			case 'Residential Attached': $property_type=2; break;
		}
		if(!empty($property_type)){
			$listing_array=get_data_array($property_type, $new_sysid);
			if(!empty($listing_array)){
				update_database($property_type, $listing_array, $new_sysid);
				patch($new_sysid);
			}
		}
	}
}else{
	echo '<br/>All listings are patched<br/>';
}

$selectSQL="SELECT SQL_CALC_FOUND_ROWS DISTINCT listings.sysid, postal_code, house_number, street_name, street_type, city, province FROM listings
LEFT JOIN listing_geoaddress ON listings.sysid=listing_geoaddress.sysid WHERE (ISNULL(lat) OR listing_geoaddress.updated='N')  AND status='A' AND property_type!='Land Only' ORDER BY RAND() LIMIT 0, 10";
$get_sysid=mysql_query_or_die($selectSQL,$useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));

if($row[0]>0){
	echo "<br/>There are ".$row[0]." listings that don't have geo code address (or don't have accurate geo code).<br/><br/>";
	//login and receive server response.
	//$response=$rets->Login();
	//var_dump($response);
	while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		$temp_sysid = $row_get_sysid['sysid'];
		//$temp_address = $row_get_sysid['address'].' '.$row_get_sysid['city'].' '.$row_get_sysid['province'];
		$temp_address=$row_get_sysid['house_number'].' '.$row_get_sysid['street_name'].' '.$row_get_sysid['street_type'].' '.$row_get_sysid['city'].' '.$row_get_sysid['province'];
		$temp_postal=$row_get_sysid['postal_code'];
		echo '<br/>Sysid to insert geocode:'.$temp_sysid.'<br/>';
		echo '<br/>Address of the sysid:'.$temp_address.'<br/>';
		echo '<br/>Postal code:'.$temp_postal.'<br/>';
		$result=get_geocode($temp_address, $temp_postal, $temp_sysid);
		echo '<br/><span style="color:#ff00ff;">'.$result.'</span><br/>';
	}
}else{
	echo "<br/>All listings have their geo code address(except those who have errors).<br/>";
}

#=====================functions=======================#

function get_data_array($property_type, $sysid){
	global $rets;
	$temp_array = $rets->GetDataArray('Property',$property_type,'(sysid='.$sysid.')',null,null);
	if(!empty($temp_array)){
		return $temp_array;
	}else{
		echo '<br/><span style="color:#ff0000;">[Warning]No listing array found</span><br/>';
		return false;
	}
}
function update_database($property_type, $listing_array, $new_sysid){
	global $useradmin, $rets;
	switch($property_type){
		case 7:
		$updateSQL = sprintf("UPDATE listings SET street_name=%s, street_type=%s, full_baths=%s, half_baths=%s, fire_places=%s, features=%s, 
			amenities=%s, basement_area=%s, num_of_floor_levels=%s, strata_maint_fee=%s WHERE sysid=%s",
			GetSQLValueString($listing_array[0][364],"text"),//street name
			GetSQLValueString($listing_array[0][365],"text"),//street type
			GetSQLValueString($listing_array[0][3735],"int"),//full baths
			GetSQLValueString($listing_array[0][3737],"int"),//half baths
			GetSQLValueString($listing_array[0][3185],"int"),//fire places
			GetSQLValueString($listing_array[0][1681],"text"),//features
			GetSQLValueString($listing_array[0][1682],"text"),//amenities
			GetSQLValueString($listing_array[0][1492],"text"),//basement area
			GetSQLValueString($listing_array[0][3087],"int"),//number of floor levels
			GetSQLValueString($listing_array[0][2967],"double"),//strata maintainence fee
			GetSQLValueString($new_sysid,"int"));
		break;
		case 1:
		$updateSQL = sprintf("UPDATE listings SET street_name=%s, street_type=%s, full_baths=%s, half_baths=%s, fire_places=%s, features=%s, 
			amenities=%s, basement_area=%s, num_of_floor_levels=%s, storeys_in_building=%s, strata_maint_fee=%s WHERE sysid=%s",
			GetSQLValueString($listing_array[0][364],"text"),//street name
			GetSQLValueString($listing_array[0][365],"text"),//street type
			GetSQLValueString($listing_array[0][166],"int"),//full_baths
			GetSQLValueString($listing_array[0][180],"int"),//half_baths
			GetSQLValueString($listing_array[0][167],"int"),//fire_places
			GetSQLValueString($listing_array[0][404],"text"),//features
			GetSQLValueString($listing_array[0][405],"text"),//amenities
			GetSQLValueString($listing_array[0][46],"text"),//basement_area
			GetSQLValueString($listing_array[0][221],"int"),//num_floor_levels
			GetSQLValueString($listing_array[0][375],"int"),//storeys_in_building
			GetSQLValueString($listing_array[0][2967],"double"),//strata_maint_fee
			GetSQLValueString($new_sysid,"int"));
		break;
		case 2:
		$updateSQL = sprintf("UPDATE listings SET street_name=%s, street_type=%s, full_baths=%s, half_baths=%s, fire_places=%s, features=%s, 
			amenities=%s, basement_area=%s, num_of_floor_levels=%s, strata_maint_fee=%s WHERE sysid=%s",
			GetSQLValueString($listing_array[0][364],"text"),//street name
			GetSQLValueString($listing_array[0][365],"text"),//street type
			GetSQLValueString($listing_array[0][536],"int"),//full_baths
			GetSQLValueString($listing_array[0][550],"int"),//half_baths
			GetSQLValueString($listing_array[0][538],"int"),//fire_places
			GetSQLValueString($listing_array[0][714],"text"),//features
			GetSQLValueString($listing_array[0][715],"int"),//amenities
			GetSQLValueString($listing_array[0][433],"text"),//basement area
			GetSQLValueString($listing_array[0][584],"int"),//num_floor_levels
			GetSQLValueString($listing_array[0][598],"double"),//strata_maint_fee
			GetSQLValueString($listing_array[0]['sysid'],"int"));
		break;
	}
	$result=mysql_query_or_die($updateSQL,$useradmin);
	echo '<br/><span style="color:#00ff00;">[info] database updated, property type:'.$property_type.'</span><br/>';
}
function patch($sysid){
	global $useradmin;
	$updateSQL=sprintf("UPDATE listings SET patched='Y' WHERE sysid=$sysid");
	$result=mysql_query_or_die($updateSQL,$useradmin);
	echo '<br/><span style="color:#00ff00;">[info] entry patched</span><br/>';
}
						

function get_geocode($address, $postal_code, $sysid){
	global $useradmin;
	$selectSQL = "SELECT * FROM listing_geoaddress WHERE sysid=".$sysid;
	$get_geocode=mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_geocode=mysql_fetch_assoc($get_geocode)&&false){
		return $row_get_geocode;
	}else{
		echo "<br/>Entering google api<br/>";
		echo '<br/>'.urlencode($address).'<br/>';
		$url="http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($address)."&components=postal_code:".urlencode($postal_code);
		echo '<br/>'.$url.'<br/>';
		$resp_json = file_get_contents($url);
		$resp = json_decode($resp_json, true);
		print_r($resp);
		echo '<br/>';
		if($resp['status']=='OK'){
			$lat=$resp['results'][0]['geometry']['location']['lat'];
			$lng=$resp['results'][0]['geometry']['location']['lng'];
			echo '<br/>'.$lat.'<br/>';
			echo '<br/>'.$lng.'<br/>';
			if(!empty($lat)&&!empty($lng)){
				$lat=trim($lat);
				$lng=trim($lng);
				$insertSQL = sprintf("INSERT INTO listing_geoaddress(sysid, lat, lng, updated)VALUES(%s,%s,%s,%s) ON DUPLICATE KEY UPDATE lat=%s, lng=%s, updated=%s",
							GetSQLValueString($sysid,"int"),
							GetSQLValueString($lat,"double"),
							GetSQLValueString($lng,"double"),
							GetSQLValueString('Y',"text"),
							GetSQLValueString($lat,"double"),
							GetSQLValueString($lng,"double"),
							GetSQLValueString('Y',"text"));
				$result=mysql_query_or_die($insertSQL,$useradmin);
				return $resp['status'];
			}else{
				$insertSQL = sprintf("INSERT IGNORE listing_geoaddress(sysid, error, updated)VALUES(%s,%s,%s)",
							GetSQLValueString($sysid,"int"),
							GetSQLValueString('Y',"text"),
							GetSQLValueString('N',"text")
							);
				$result=mysql_query_or_die($insertSQL,$useradmin);
				return false;
			}
			
		}elseif($resp['status']=='OVER_QUERY_LIMIT'){
			return $resp['status'];
		}else{
			$insertSQL = sprintf("INSERT INTO listing_geoaddress(sysid, error, updated)VALUES(%s,%s,%s) ON DUPLICATE KEY UPDATE error=%s, updated=%s",
							GetSQLValueString($sysid,"int"),
							GetSQLValueString('Y',"text"),
							GetSQLValueString('N',"text"),
							GetSQLValueString('Y',"text"),
							GetSQLValueString('Y',"text"));
			$result=mysql_query_or_die($insertSQL,$useradmin);
			return $resp['status'];
		}
	}
}


function get_column_name($column_number){
	switch($column_number){
		case 186:$column_name="# of Images"; break;
		case 14:$column_name="Address"; break;
		case 3976:$column_name="Address AWP Display"; break;
		case 34:$column_name="Age"; break;
		case 405:$column_name="Amenities"; break;
		case 16:$column_name="Approx. Year Built"; break;
		case 2283:$column_name="Area"; break;
		case 2233:$column_name="Area Desc"; break;
		case 46:$column_name="Basement Area"; break;
		case 43:$column_name="Board ID"; break;
		case 28:$column_name="Broker Recip Flag"; break;
		case 3794:$column_name="City"; break;
		case 2667:$column_name="Depth"; break;
		case 2317:$column_name="Display Address on Internet"; break;
		case 404:$column_name="Features Included"; break;
		case 167:$column_name="Fireplaces"; break;
		case 3922:$column_name="Floor Area -Grand Total"; break;
		case 249:$column_name="Floor Area Fin - Main Floor"; break;
		case 2651:$column_name="For Tax Year"; break;
		case 2653:$column_name="Frontage"; break;
		case 2655:$column_name="Frontage - Metric"; break;
		case 166:$column_name="Full Baths"; break;
		case 2673:$column_name="Gross Taxes"; break;
		case 180:$column_name="Half Baths"; break;
		case 181:$column_name="House#"; break;
		case 2311:$column_name="House # Alpha"; break;
		case 2923:$column_name="Internet Remarks"; break;
		case 30:$column_name="Last Img Trans Date"; break;
		case 217:$column_name="Last Trans Date"; break;
		case 224:$column_name="List Date"; break;
		case 222:$column_name="List Firm 1 Code"; break;
		case 2675:$column_name="List Firm 1 FAX"; break;
		case 2679:$column_name="List Firm 1 Name"; break;
		case 2681:$column_name="List Firm 1 Phone"; break;
		case 2685:$column_name="List Firm 1 URL"; break;
		case 2689:$column_name="List Firm 2 Code"; break;
		case 2677:$column_name="List Firm 2 FAX"; break;
		case 2325:$column_name="List Firm 2 Name"; break;
		case 2683:$column_name="List Firm 2 Phone"; break;
		case 2687:$column_name="List Firm 2 URL"; break;
		case 227:$column_name="List or Sell Agent"; break;
		case 228:$column_name="List or Sell Firm"; break;
		case 226:$column_name="List Price"; break;
		case 342:$column_name="List Realtor 1 ID"; break;
		case 2703:$column_name="List Realtor 1 Name"; break;
		case 2711:$column_name="List Realtor 1 Phone"; break;
		case 2705:$column_name="List Realtor 1 URL"; break;
		case 2697:$column_name="List Realtor 2 ID"; break;
		case 2327:$column_name="List Realtor 2 Name"; break;
		case 2713:$column_name="List Realtor 2 Phone"; break;
		case 2707:$column_name="List Realtor 2 URL"; break;
		case 2699:$column_name="List Realtor 3 ID"; break;
		case 2329:$column_name="List Realtor 3 Name"; break;
		case 3893:$column_name="List Realtor 3 Office ID"; break;
		case 3895:$column_name="List Realtor 3 Office Name"; break;
		case 2717:$column_name="List Realtor 3 Phone"; break;
		case 2701:$column_name="List Realtor 3 URL"; break;
		case 2453:$column_name="Lot Sz (Acres)"; break;
		case 2455:$column_name="Lot Sz (Hectares)"; break;
		case 2457:$column_name="Lot Sz (Sq.Ft.)"; break;
		case 2460:$column_name="Lot Sz (Sq.Mtrs.)"; break;
		case 248:$column_name="MLS Number"; break;
		case 221:$column_name="No. Floor Levels"; break;
		case 11:$column_name="Postal Code"; break;
		case 1:$column_name="Property Type"; break;
		case 88:$column_name="Province"; break;
		case 411:$column_name="Public Remarks"; break;
		case 3985:$column_name="Public Remarks 2"; break;
		case 3:$column_name="Publish Listing on Internet"; break;
		case 3926:$column_name="Site Influences"; break;
		case 363:$column_name="Status"; break;
		case 375:$column_name="Storeys in Building"; break;
		case 2967:$column_name="Strata Maint Fee"; break;
		case 94:$column_name="Street Dir"; break;
		case 364:$column_name="Street Name"; break;
		case 365:$column_name="Street Type"; break;
		case 2731:$column_name="Style of Home"; break;
		case 2568:$column_name="Sub-Area/Community"; break;
		case 2570:$column_name="Sub-Area/Community Desc"; break;
		case 2737:$column_name="Title to Land"; break;
		case 3928:$column_name="Total Baths"; break;
		case 378:$column_name="Total Bedrooms"; break;
		case 2733:$column_name="Type of Dwelling"; break;
		case 2971:$column_name="Unit #"; break;
		case 3798:$column_name="View"; break;
		case 2975:$column_name="View - Specify"; break;
		case 26:$column_name="Virtual Tour URL"; break;
		case "sysid":$column_name="sysid"; break;
		default: $column_name="unknown column".$column_number;
	}
	return $column_name;
}

function get_open_house(){
	global $useradmin, $current_YVR_date;
	$selectSQL = "SELECT sysid, open_house, last_trans_date, public_remarks, public_remarks_2 FROM listings WHERE (LOWER(public_remarks) LIKE '%open house%' OR LOWER(public_remarks_2) LIKE '%open house%') AND status='A' AND !ISNULL(listings.album_id) AND listings.property_type!='Land Only' AND area_desc!='VOT' AND area_desc!='FOT' AND DATE(last_trans_date)='".$current_YVR_date."'";
	$get_listing = mysql_query_or_die($selectSQL, $useradmin);
	//$string_array=array();
	while($row_get_listing=mysql_fetch_assoc($get_listing)){
		//echo "<br/><h1>".$row_get_listing[sysid]."</h1><br/>";
		$description=$row_get_listing['public_remarks'].$row_get_listing['public_remarks_2'];
		$pattern='/(?i)open house(.+)/';
		preg_match($pattern, $description, $matches);
		$date_string=process_date($matches[1]);
		
		if(!empty($date_string)){
			$last_trans_year=date('Y',strtotime($row_get_listing['last_trans_date']));
			$open_house_date=$last_trans_year.'-'.$date_string;
			$temp_array=array("original"=>$matches[1],"date"=>$open_house_date);
			if(isset($open_house_date)&&!empty($open_house_date)){
				if(!empty($row_get_listing['open_house'])){
					if($open_house_date!=$row_get_listing['open_house']){
						echo '<br/>'.$row_get_listing['sysid'].' already has an open house date but a new open house date is set.Updating...<br/>';
						$updateSQL = sprintf("UPDATE listings SET open_house=%s WHERE sysid=%s",
						GetSQLValueString($open_house_date,"date"),
						GetSQLValueString($row_get_listing['sysid'],"int"));
						$result = mysql_query_or_die($updateSQL, $useradmin);
					}else{
						echo '<br/>'.$row_get_listing['sysid'].' already has an open house date, and it is the most updated version...<br/>';
					}
					
				}else{
					echo '<br/>'.$row_get_listing['sysid'].' does not have an open house date.<br/>';
					$updateSQL = sprintf("UPDATE listings SET open_house=%s WHERE sysid=%s",
					GetSQLValueString($open_house_date,"date"),
					GetSQLValueString($row_get_listing['sysid'],"int"));
					$result = mysql_query_or_die($updateSQL, $useradmin);
				}
			}
			unset($open_house_date);
		}
		
		
		//array_push($string_array, $temp_array);
	}
	//echo json_encode($string_array);
}

function process_date($string){
	$return_string;
	$temp_array=explode(',',$string);
	$date_array=array();
	for($i=0;$i<count($temp_array);$i++){
		$weekday_pattern="/(?:sat|sun|mon|tue|wed|thu|fri)\.?(?:\w*day)?\s?/i";
		$temp_array[$i]=preg_replace($weekday_pattern,'',$temp_array[$i]);
		$temp_array[$i]=preg_replace('/[^A-Za-z0-9\-\s\/]/', '', $temp_array[$i]);
		$temp_array[$i]=preg_replace('/\s?\d{0,2}[:]?\d{0,2}\s?(am|pm)?\s?-+\s?\d{0,2}[:]?\d{0,2}\s?(am|pm)/i', '', $temp_array[$i]);
		// on may 18
		//echo '<br/>'.$temp_array[$i].'<br/>';
		$pattern_1='/(?i)\s+on\s+([\w\d\s]+)/';
		preg_match($pattern_1,$temp_array[$i],$matches_1);
		if(count($matches_1)>1){
			if ($date=strtotime( $matches_1[1])){
				//echo $temp_array[$i].' : '.date('m-d',$date).'<br/>';
				array_push($date_array,date('m-d',$date));
			}
		}
		
		//Saturday May 18th
		$pattern_2='/\s{1,}(\w+\s+\d+)\w*/';
		preg_match($pattern_2,$temp_array[$i],$matches_2);
		if(count($matches_2)>1){
			if ($date=strtotime( $matches_2[1])){
				//echo $temp_array[$i].' : '.date('m-d',$date).'<br/>';
				array_push($date_array,date('m-d',$date));
			}
		}
		if(count($matches_1)>1&&count($matches_2)>1){
			$replace_array=array($matches_1[1],$matches_2[1]);
		}
		if(isset($replace_array)&&is_array($replace_array)){
			$temp_array[$i]=trim(str_replace($replace_array,'',$temp_array[$i]));
			//echo '<br/>after matching|'.$temp_array[$i].'|<br/>';
			//nothing
		}
		$temp_array[$i]=strtoupper($temp_array[$i]);
		if (!is_numeric($temp_array[$i])&&!empty($temp_array[$i])&&$date=strtotime($temp_array[$i])){
			//echo '<br/>'.$temp_array[$i].' : '.date('m-d',$date).'<br/>';
			array_push($date_array,date('m-d',$date));
		}
		
	}
	if(isset($date_array)&&count($date_array)>0){
		$return_string=$date_array[0];
		return $return_string;
	}else{
		return false;
	}
}


?>