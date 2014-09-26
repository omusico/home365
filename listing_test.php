<?php
require_once 'dbconnect/dbconnect.php';
require_once 'utilities/utilities.php';
require_once 'RETS.php';

if(mysql_select_db("home365_ios",$useradmin)){
	
}else{
	echo "Error selecting database, exited.";
	exit();
}
$current_GMT_time=get_GMT(time());
$current_GMT_time_string=date("Y-m-d H:i:s",$current_GMT_time);
$current_YVR_time=getTimeByCity($current_GMT_time,'YVR');
$current_YVR_time_string=date("Y-m-d H:i:s",$current_YVR_time);
$number=get_num_of_photos(260976779);
echo $number;
$selectSQL = "SELECT SQL_CALC_FOUND_ROWS sysid FROM listings WHERE ISNULL(num_of_photos)";
//$selectSQL = "SELECT sysid FROM listings WHERE status='A'";
$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
	$temp_sysid = $row_get_sysid['sysid'];
	$num_of_photos=get_num_of_photos($temp_sysid);
	$updateSQL = sprintf("UPDATE listings SET num_of_photos=%s WHERE sysid=%s",
						GetSQLValueString($num_of_photos,"int"),
						GetSQLValueString($temp_sysid,"int"));
	echo '<br/> updating: '.$temp_sysid.'<br/>';
	$result = mysql_query_or_die($updateSQL, $useradmin);
}
function get_num_of_photos($sysid){
	global $useradmin;
	$selectSQL="SELECT SQL_CALC_FOUND_ROWS listings.sysid, listings.album_id, listing_album.album_id, album_profile.photo_id, photo_profile.photo_id, photo_path FROM listings".
		" LEFT JOIN listing_album ON listings.sysid=listing_album.sysid".
		" LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id".
		" LEFT JOIN photo_profile ON album_profile.photo_id=photo_profile.photo_id".
		" WHERE listings.sysid=$sysid AND !ISNULL(photo_path) GROUP BY photo_path";
	$result = mysql_query_or_die($selectSQL, $useradmin);
	$row = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	if(is_array($row)&&!empty($row)){
		return $row[0];
	}else{
		return false;
	}
}


?>