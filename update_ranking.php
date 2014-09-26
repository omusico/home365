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

$selectSQL = "SELECT sysid, ranking, num_of_photos, date_imported_local FROM listings".
			" LEFT JOIN city_profile ON listings.city=city_profile.en".
			" WHERE status='A' AND !ISNULL(listings.album_id) AND listings.property_type!='Land Only' AND area_desc!='VOT' AND area_desc!='FOT'".
			" ORDER BY date_imported_local DESC";
$get_listing = mysql_query_or_die($selectSQL, $useradmin);
while ($row_get_listing=mysql_fetch_assoc($get_listing)){
	if($row_get_listing['num_of_photos']==null){
		$row_get_listing['num_of_photos']=0;
	}
	$list_ranking=calculate_ranking($row_get_listing['date_imported_local'],$row_get_listing['ranking'],$row_get_listing['num_of_photos']).'</td>';
	insert_update_ranking($row_get_listing['sysid'],$list_ranking);
}
//$result_html.='</table>';
//echo $result_html;
function calculate_ranking($date, $city_ranking, $number){
	
	$date_timestamp = strtotime($date);
	$compare_time_stamp = strtotime('2000-01-01 00:00:00');
	$t=$date_timestamp-$compare_time_stamp;
	$r=$city_ranking;
	$x=$number;
	$y=($x>1?1.901:1.9);
	$z=($x>0?$x:1);
	$ranking=($y*$t)/900+log10($z)+$r;
	return  number_format((float)$ranking, 2, '.', '');
}
function insert_update_ranking($sysid, $ranking){
	global $useradmin;
	$selectSQL = "SELECT sysid FROM listing_ranking WHERE sysid=$sysid";
	$get_sysid=mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		echo "<br/>sysid".$row_get_sysid['sysid']."is already in the database<br/>";
		$updateSQL = sprintf("UPDATE listing_ranking SET ranking=%s WHERE sysid=%s",
					GetSQLValueString($ranking,"double"),
					GetSQLValueString($row_get_sysid['sysid'],"int"));
		$result=mysql_query_or_die($updateSQL, $useradmin);
		
	}else{
		echo "<br/>$sysid is not in the database, inserting new record<br/>";
		$insertSQL = sprintf("INSERT INTO listing_ranking (sysid, ranking) VALUES (%s, %s)",
		GetSQLValueString($sysid,"int"),
		GetSQLValueString($ranking,"double"));
		$result=mysql_query_or_die($insertSQL, $useradmin);
	}
}
?>