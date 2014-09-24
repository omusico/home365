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
$size=isset($_POST['size'])?$_POST['size']:$_GET['size'];
if(empty($size)){
	$size=20;
}
$limit=" LIMIT $from, $size";
if($size=="all"){
	$limit='';
}
$mls=isset($_POST['mls'])?$_POST['mls']:$_GET['mls'];
$area=isset($_POST['area'])?$_POST['area']:$_GET['area'];
$type=isset($_POST['type'])?$_POST['type']:$_GET['type'];
$built_year=isset($_POST['built_year'])?$_POST['built_year']:$_GET['built_year'];
$bedrooms=isset($_POST['bedrooms'])?$_POST['bedrooms']:$_GET['bedrooms'];
$bathrooms=isset($_POST['bathrooms'])?$_POST['bathrooms']:$_GET['bathrooms'];
$price_min=doubleval(isset($_POST['price_min'])?$_POST['price_min']:$_GET['price_min']);
$price_max=doubleval(isset($_POST['price_max'])?$_POST['price_max']:$_GET['price_max']);
$floor_min=doubleval(isset($_POST['floor_min'])?$_POST['floor_min']:$_GET['floor_min']);
$floor_max=doubleval(isset($_POST['floor_max'])?$_POST['floor_max']:$_GET['floor_max']);
$top=doubleval(isset($_POST['top'])?$_POST['top']:$_GET['top']);
$down=doubleval(isset($_POST['down'])?$_POST['down']:$_GET['down']);
$left=doubleval(isset($_POST['left'])?$_POST['left']:$_GET['left']);
$right=doubleval(isset($_POST['right'])?$_POST['right']:$_GET['right']);
$sort=isset($_POST['sort'])?$_POST['sort']:$_GET['sort'];
$order='';
if(!empty($sort)){
	switch($sort){
		case 'price_high':
		$order= ' list_price DESC,';
		break;
		case 'price_low':
		$order= ' list_price ASC,';
		break;
		case 'default':
		$order.='';
		break;
	}
}

$current_GMT_time=get_GMT(time());
$current_GMT_time_string=date("Y-m-d H:i:s",$current_GMT_time);
$current_YVR_time=getTimeByCity($current_GMT_time,'YVR');
$current_YVR_time_string=date("Y-m-d H:i:s",$current_YVR_time);
$current_YVR_date=date("Y-m-d",$current_YVR_time);

$filter='';
if(!empty($mls)){
	$filter.=" AND mls_number ='".$mls."'";
}
if(!empty($built_year)){
	$filter.=" AND built_year>=$built_year";
}
if(!empty($bedrooms)){
	$filter.=" AND bedrooms>=$bedrooms";
}
if(!empty($bathrooms)){
	$filter.=" AND bathrooms>=$bathrooms";
}
if(!empty($area)){
	switch($area){
		case "vancouverwest":
		$filter.=" AND area_desc ='VVW'";
		break;
		case "vancouvereast":
		$filter.=" AND area_desc='VVE'";
		break;
		case "all":
		break;
		default:
		$city_array=get_city($area);
		//var_dump($city_array);
		$filter.=" AND";
		for($i=0;$i<count($city_array);$i++){
			$filter.=($i==0?'':' OR')." LOWER(listings.city)='".$city_array[$i]['en']."'";
		}
		if(1>count($city_array)){
			//echo "<br/>city array has no value<br/>";
			$filter.=" address LIKE '%".$area."%'";
		}
		break;
	}
	//$filter.=" AND LOWER(listings.city) ='".$area."'";
}
if(!empty($type)){
	switch($type){
		case 'house': $new_type='House/Single Family'; break;
		case 'townhouse': $new_type='Townhouse'; break;
		case 'apartment': $new_type='Apartment/Condo'; break;
		case 'duplex': $new_type='1/2 Duplex'; break;
	}
	$filter.=" AND LOWER(listings.type_of_dwelling) ='".$new_type."'";
}
if(!empty($floor_min)){
	$filter.=" AND listings.floor_area_total >=".$floor_min;
}
if(!empty($floor_max)&&$floor_max>=$floor_min){
	$filter.=" AND listings.floor_area_total <=".$floor_max;
}
if(!empty($price_min)){
	$filter.=" AND listings.list_price >=".$price_min;
}
if(!empty($price_max)&&$price_max>=$price_min){
	$filter.=" AND listings.list_price <=".$price_max;
}
if(!empty($top)&&!empty($down)&&!empty($right)&&!empty($left)){
	$filter.=" AND listing_geoaddress.lat>=".$down;
	$filter.=" AND listing_geoaddress.lat<=".$top;
	$filter.=" AND listing_geoaddress.lng>=".$left;
	$filter.=" AND listing_geoaddress.lng<=".$right;
}
get_open_house();

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
		$last_trans_year=date('Y',strtotime($row_get_listing['last_trans_date']));
		if(!empty($date_string)){
			$open_house_date=$last_trans_year.'-'.$date_string;
		}
		$temp_array=array("original"=>$matches[1],"date"=>$open_house_date);
		if(!empty($open_house_date)){
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
		if ($date=strtotime( $matches_1[1])){
			//echo $temp_array[$i].' : '.date('m-d',$date).'<br/>';
			array_push($date_array,date('m-d',$date));
		}
		//Saturday May 18th
		$pattern_2='/\s{1,}(\w+\s+\d+)\w*/';
		preg_match($pattern_2,$temp_array[$i],$matches_2);
		if ($date=strtotime( $matches_2[1])){
			//echo $temp_array[$i].' : '.date('m-d',$date).'<br/>';
			array_push($date_array,date('m-d',$date));
		}
		$replace_array=array($matches_1[1],$matches_2[1]);
		$temp_array[$i]=trim(str_replace($replace_array,'',$temp_array[$i]));
		//echo '<br/>after matching|'.$temp_array[$i].'|<br/>';
		//nothing
		$temp_array[$i]=strtoupper($temp_array[$i]);
		if (!is_numeric($temp_array[$i])&&!empty($temp_array[$i])&&$date=strtotime($temp_array[$i])){
			//echo '<br/>'.$temp_array[$i].' : '.date('m-d',$date).'<br/>';
			array_push($date_array,date('m-d',$date));
		}
		
	}
	$return_string=$date_array[0];
	return $return_string;
}

?>