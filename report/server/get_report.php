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

$start_date=isset($_POST['start'])?$_POST['start']:$_GET['start'];
$end_date=isset($_POST['end'])?$_POST['end']:$_GET['end'];
$function=isset($_POST['function'])?$_POST['function']:$_GET['function'];
$start_pieces=explode("/",$start_date);
$start_date=$start_pieces[2].'-'.$start_pieces[0].'-'.$start_pieces[1];
$end_pieces=explode("/",$end_date);
$end_date=$end_pieces[2].'-'.$end_pieces[0].'-'.$end_pieces[1];
if(!empty($function)){
	switch($function){
		case "daily":
			$data_array=get_daily_report($start_date, $end_date);
		break;
		case "weekly":
			$data_array=get_weekly_report($start_date, $end_date);
		break;
		default:
		$data_array=get_daily_report($start_date, $end_date);
	}
}else{
	$data_array=get_daily_report($start_date, $end_date);
}


$return_array=array("report"=>$data_array);
echo json_encode($return_array);

function get_daily_report($start_date, $end_date){
	global $useradmin;
	$start = new DateTime($start_date);
	$end = new DateTime($end_date);
	$end = $end->modify( '+1 day' ); 
	$interval = new DateInterval('P1D');
	$period = new DatePeriod($start, $interval ,$end);
	
	$daily_visits_array=array();
	$daily_unique_visitors_array=array();
	$daily_avg_time_array=array();
	foreach($period as $date){
		$index_date=$date->format('Y/m/d');
		$selectSQL = "SELECT * FROM visitor_daily_report WHERE DATE(report_date)='".$index_date."'";
		$get_record=mysql_query_or_die($selectSQL,$useradmin);
		if($row_get_record=mysql_fetch_assoc($get_record)){
			$daily_visits_array[$index_date]=$row_get_record['visits'];
			$daily_unique_visitors_array[$index_date]=$row_get_record['unique_visitors'];
			$daily_avg_time_array[$index_date]=ceil($row_get_record['total_time']/$row_get_record['visits']);
		}else{
			
			$daily_visits_array[$index_date]=0;
			$daily_unique_visitors_array[$index_date]=0;
			$daily_avg_time_array[$index_date]=0;
		}
	}
	$return_array=array("visits"=>$daily_visits_array,"unique_visitors"=>$daily_unique_visitors_array,"avg_time"=>$daily_avg_time_array);
	
	return $return_array;
}

function get_weekly_report($start_date, $end_date){
	global $useradmin;
	$end_date = strtotime($end_date);
	$daily_visits_array=array();
	$daily_unique_visitors_array=array();
	
	for($i = strtotime('Last Monday', strtotime($start_date)); $i <= $end_date;){
		$from= date('Y-m-d', $i);
		$i = strtotime('+1 week', $i);
		if($i<=$end_date){
			$to=date('Y-m-d', $i);
		}else{
			$to=date('Y-m-d', $end_date);
		}
		$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT device_id FROM visitor_log WHERE DATE(time_stamp)>='$from' AND DATE(time_stamp)<='$to' AND user_state='start'";
		$get_record=mysql_query_or_die($selectSQL,$useradmin);
		$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
		$visits=isset($row[0])?$row[0]:0;
		$selectSQL = "SELECT SQL_CALC_FOUND_ROWS device_id FROM visitor_log WHERE DATE(time_stamp)>='$from' AND DATE(time_stamp)<='$to' AND user_state='start'";
		$get_record=mysql_query_or_die($selectSQL,$useradmin);
		$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
		$unique_visitors=isset($row[0])?$row[0]:0;
		$daily_visits_array[$from]=$visits;
		$daily_unique_visitors_array[$from]=$unique_visitors;
		
		
	}
	$return_array=array("visits"=>$daily_visits_array,"unique_visitors"=>$daily_unique_visitors_array);
	return $return_array;
}


?>
