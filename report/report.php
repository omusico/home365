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
$current_GMT_time=get_GMT(time());
$current_GMT_time_string=date("Y-m-d H:i:s",$current_GMT_time);
$current_YVR_time=getTimeByCity($current_GMT_time,'YVR');
$current_YVR_time_string=date("Y-m-d H:i:s",$current_YVR_time);
$current_YVR_date=date("Y-m-d",$current_YVR_time);
$current_YVR_date_object=new DateTime($current_YVR_date);

$start_date=date('Y-m-d', strtotime('-7 days'));
$end_date=date('Y-m-d', strtotime('-1 day'));
record($start_date,$end_date);
function record($start_date, $end_date){
	global $useradmin;
	$start = new DateTime($start_date);
	$end = new DateTime($end_date);
	$end = $end->modify( '+1 day' ); 
	$interval = new DateInterval('P1D');
	$period = new DatePeriod($start, $interval ,$end);
	//var_dump($period);
	
	foreach($period as $date){
		$index_date=$date->format('Y-m-d');
		//echo $index_date;
		
		$unique_visitors=get_unique_visits($index_date);
		$visits=get_visits($index_date);
		$time=get_time($index_date);
		echo "<br/><br/>".$index_date."<br/><br/>";
		echo "unique visitors:".$unique_visitors."<br/>";
		echo "visits:".$visits."<br/>";
		echo "total time:".$time."<br/>";
		$selectSQL = "SELECT * FROM visitor_daily_report WHERE report_date='".$index_date."'";
		if($row_record=mysql_fetch_assoc(mysql_query_or_die($selectSQL, $useradmin))){
			echo "<br/>record already in database!<br/>";
		}else{
			echo "<br/>inserting new record into database!<br/>";
			$insertSQL= sprintf("INSERT INTO visitor_daily_report (report_date, visits, unique_visitors, total_time) VALUES(%s, %s, %s, %s)",
						GetSQLValueString($index_date,"date"),
						GetSQLValueString($visits,"int"),
						GetSQLValueString($unique_visitors,"int"),
						GetSQLValueString($time,"int"));
			$result=mysql_query_or_die($insertSQL, $useradmin);
		}
	}

}
function get_visits($date){
	global $useradmin;
	$selectSQL = "SELECT SQL_CALC_FOUND_ROWS device_id FROM visitor_log WHERE DATE(time_stamp)='".$date."'".
		" AND user_state='start'";
	$get_record=mysql_query_or_die($selectSQL,$useradmin);
	$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	return $row[0];
}
function get_unique_visits($date){
	global $useradmin;
	$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT device_id FROM visitor_log WHERE DATE(time_stamp)='".$date."'".
		" AND user_state='start'";
	$get_record=mysql_query_or_die($selectSQL,$useradmin);
	$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	return $row[0];
}

function get_time($date){
	global $useradmin;
	$selectSQL = "SELECT DISTINCT device_id from visitor_log WHERE DATE(time_stamp)='$date' AND user_state='start'";
	$get_result = mysql_query_or_die($selectSQL, $useradmin);
	$total_time=0;
	while($row_get_result=mysql_fetch_assoc($get_result)){
		$temp_device_id=$row_get_result['device_id'];
		$selectSQL = "SELECT * FROM visitor_log WHERE device_id='$temp_device_id' AND DATE(time_stamp)='$date' ORDER BY time_stamp ASC";
		$result = mysql_query_or_die($selectSQL, $useradmin);
		while ($row_result=mysql_fetch_assoc($result)){
			if($row_result['user_state']=='start'){
				$selectSQL = "SELECT * FROM visitor_log WHERE device_id='$temp_device_id' AND time_stamp>'".$row_result['time_stamp']."' ORDER BY time_stamp ASC LIMIT 0, 1";
				$next_entry = mysql_fetch_assoc(mysql_query_or_die($selectSQL, $useradmin));
				if($next_entry['user_state']=='end'){
					$selectSQL="SELECT TIMESTAMPDIFF(SECOND, '".$row_result['time_stamp']."', '".$next_entry['time_stamp']."') AS result";
					$duration=mysql_fetch_assoc(mysql_query_or_die($selectSQL,$useradmin));
					
					$total_time+=$duration['result'];
				}else{
					
				}
			}

		}

	}
	return $total_time;
	
}

?>