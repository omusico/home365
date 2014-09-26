<?php 

error_reporting(E_ALL);
$document_root='/home/home365/public_html';
$domain_name='http://www.home365.ca/';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

$start_date='2014-05-01';
$end_date='2014-06-10';
var_dump(get_weekly_report($start_date, $end_date));
function get_weekly_report($start_date, $end_date){
	global $useradmin;
	$end_date = strtotime($end_date);
	$return_array=array();
	for($i = strtotime('Monday', strtotime($start_date)); $i <= $end_date;){
		$from= date('Y-m-d', $i);
		$i = strtotime('+1 week', $i);
		$to=date('Y-m-d', $i);
		$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT device_id FROM visitor_log WHERE DATE(time_stamp)>='$from' AND DATE(time_stamp)<='$to' AND user_state='start'";
		$get_record=mysql_query_or_die($selectSQL,$useradmin);
		$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
		$return_array[$from]=$row[0];
	}
	return $return_array;
}


?>