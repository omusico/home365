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
$current_YVR_date=date("Y-m-d", $current_YVR_time);
$insert_size=0;

//login and receive server response.
$response=$rets->Login();
var_dump($response);


//The following code is to test if the cron job is executed.
$file="file.txt";
$current=file_get_contents($file);
$current.="$current_YVR_time_string from sysid_test.php\n";
file_put_contents($file,$current);
update_current();
update_new();
update_recent_update();
update_recent_image();
get_open_house();
/*
*Functions
*/
function update_current(){
	global $rets, $useradmin, $insert_size;
	$sysid_array=$rets->GetDataArray('Property','11','(363=|A)', 'sysid', null);
	$truncateSQL = "TRUNCATE TABLE sysid_raw_current";
	$result=mysql_query_or_die($truncateSQL, $useradmin);
	foreach($sysid_array as $index=>$array){
		insert_sysid($array['sysid']);
	}
	echo "<br/>$insert_size records have been inserted to the database<br/>";
}
function update_new(){
	global $rets, $useradmin,$current_YVR_date,$current_YVR_time_string;
	$truncateSQL = "TRUNCATE TABLE sysid_raw_new";
	$result=mysql_query_or_die($truncateSQL, $useradmin);
	$insertSQL = 'INSERT INTO sysid_raw_new SELECT sysid_raw_current.sysid FROM sysid_raw_current LEFT JOIN listings ON sysid_raw_current.sysid=listings.sysid WHERE ISNULL(listings.sysid)';
	$result = mysql_query_or_die($insertSQL, $useradmin);
	$insertSQL = "INSERT INTO sysid_raw_new_count (num_of_new_sysid, date_created)SELECT COUNT(*),'".$current_YVR_time_string."' FROM sysid_raw_new";
	//echo $insertSQL;
	$result=mysql_query_or_die($insertSQL, $useradmin);
}

function update_recent_update(){
	global $rets, $useradmin, $current_YVR_date;
	$updated_sysid_array=$rets->GetDataArray('Property','11','(363=|A),(217='.$current_YVR_date.'T00:00:00-'.$current_YVR_date.'T23:59:59)','sysid,217',null);
	
	$truncateSQL = "TRUNCATE TABLE sysid_raw_recent_update";
	$result=mysql_query_or_die($truncateSQL, $useradmin);
	foreach($updated_sysid_array as $index=>$array){
		$insertSQL = sprintf("INSERT INTO sysid_raw_recent_update (sysid, last_trans_date) VALUES(%s, %s)",
			GetSQLValueString($array['sysid'],"int"),
			GetSQLValueString($array[217],"date"));
		$result = mysql_query_or_die($insertSQL, $useradmin);
		
	}
}
function update_recent_image(){
	global $rets, $useradmin,$current_YVR_date,$current_YVR_time;
	$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sysid FROM sysid_raw_recent_image_update";
	$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
	$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	if($row[0]==0){
		$updated_image_sysid_array=$rets->GetDataArray('Property','11','(363=|A),(30='.$current_YVR_date.'T00:00:00-'.$current_YVR_date.'T23:59:59)','sysid,186,30',null);
		foreach($updated_image_sysid_array as $index=>$array){
			$last_trans_time=strtotime($array[30]);
			
			if($current_YVR_time-$last_trans_time<3600){
				$insertSQL = sprintf("INSERT INTO sysid_raw_recent_image_update (sysid, num_of_photos, last_img_trans_date) VALUES(%s, %s, %s)",
					GetSQLValueString($array['sysid'],"int"),
					GetSQLValueString($array[186],"int"),
					GetSQLValueString($array[30],"date"));
					//echo $insertSQL."<br/>";
					
				$result = mysql_query_or_die($insertSQL, $useradmin);
			}
		}
	}

}
function insert_sysid($sysid){
	global $useradmin, $insert_size, $current_YVR_time_string;
	$selectSQL = "SELECT * FROM sysid_raw_current WHERE sysid=$sysid";
	$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		echo "<br/>Record $sysid is already in the database<br/>";
	}else{
		$insertSQL = sprintf("INSERT INTO sysid_raw_current (sysid, date_created_local) VALUES(%s, %s)",
		GetSQLValueString($sysid,"int"),
		GetSQLValueString($current_YVR_time_string,"text"));
		$result = mysql_query_or_die($insertSQL, $useradmin);
		$insert_size++;
	}
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
		
		//Saturday May 18th
		$pattern_2='/\s{1,}(\w+\s+\d+)\w*/';
		preg_match($pattern_2,$temp_array[$i],$matches_2);
		if(isset($matches_1)){
			if ($date=strtotime( $matches_1[1])){
				//echo $temp_array[$i].' : '.date('m-d',$date).'<br/>';
				array_push($date_array,date('m-d',$date));
			}
		}

		if(isset($matches_2)){
			if ($date=strtotime( $matches_2[1])){
				//echo $temp_array[$i].' : '.date('m-d',$date).'<br/>';
				array_push($date_array,date('m-d',$date));
			}
			
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