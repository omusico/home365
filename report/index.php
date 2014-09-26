<?php
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

//update the daily report table first
$selectSQL = "SELECT report_date FROM visitor_daily_report ORDER BY report_date DESC LIMIT 0, 1";
$get_date = mysql_query_or_die($selectSQL, $useradmin);
if($row_get_date=mysql_fetch_assoc($get_date)){
	$start_date = $row_get_date['report_date'];
}

$end_date=date('Y-m-d', strtotime('-1 day'));
record($start_date, $end_date);


//begin to retrieve data. Default:recent 7 days
$seven_days_ago=date('Y-m-d', strtotime('-7 days'));
////$current_YVR_date_object->modify('first day of this month')->format('Y-m-d');
//echo $first_this_month;
$data_array=get_data($seven_days_ago,$current_YVR_date);



$today_visits=get_visits($current_YVR_date);
$today_visitor="<h2>今日访问次数: ".$today_visits."</h2>";
$today_unique_visits=get_unique_visits($current_YVR_date);
$today_visitor.="<h2>今日访问量: ".$today_unique_visits."</h2>";
$today_visitor.="<h2>人均访问次数: ".number_format($today_visits/$today_unique_visits,3)."</h2>";
$total_time=get_time($current_YVR_date);
$total_time_string=convert_time($total_time);
$average_time=ceil($total_time/$today_visits);
$average_time_string=convert_time($average_time);
$average_time_per_visit=ceil($total_time/$today_unique_visits);
$average_time_per_visit_string=convert_time($average_time_per_visit);
$today_visitor.="<h2>访问总时长: ".$total_time_string."</h2>";
$today_visitor.="<h2>每次访问时长: ".$average_time_string."</h2>";
$today_visitor.="<h2>当日人均累计访问时长: ".$average_time_per_visit_string."</h2>";
$today_visits_array=array();
$today_unique_visits_array=array();
$today_avg_time_array=array();
$today_visits_array[$current_YVR_date]=$today_visits;
$today_unique_visits_array[$current_YVR_date]=$today_unique_visits;
$today_avg_time_array[$current_YVR_date]=$average_time;
$today_report_array=array("visits"=>$today_visits_array,"unique_visits"=>$today_unique_visits_array,"avg_time"=>$today_avg_time_array);
$data_array['visits'][$current_YVR_date]=$today_visits;
$data_array['unique_visitors'][$current_YVR_date]=$today_unique_visits;
//$data_array['avg_time'][$current_YVR_date]=$average_time;
function get_data($start_date, $end_date){
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
		$index_date=$date->format('Y-m-d');
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

function convert_time($time){
	if($time>3600){
		$time=number_format($time/3600,1)."小时";
	}elseif($time>60){
		$time=number_format($time/60, 2)."分钟";
	}else{
		$time=$time."秒";
	}
	return $time;
}
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
		//echo "<br/><br/>".$index_date."<br/><br/>";
		//echo "unique visitors:".$unique_visitors."<br/>";
		//echo "visits:".$visits."<br/>";
		//echo "total time:".$time."<br/>";
		$selectSQL = "SELECT * FROM visitor_daily_report WHERE report_date='".$index_date."'";
		if($row_record=mysql_fetch_assoc(mysql_query_or_die($selectSQL, $useradmin))){
			//echo "<br/>record already in database!<br/>";
			$updateSQL=sprintf("UPDATE visitor_daily_report SET visits=%s, unique_visitors=%s, total_time=%s WHERE report_date=%s",
			GetSQLValueString($visits,"int"),
			GetSQLValueString($unique_visitors,"int"),
			GetSQLValueString($time,"int"),
			GetSQLValueString($index_date,"date"));
			$result=mysql_query_or_die($updateSQL, $useradmin);
		}else{
			//echo "<br/>inserting new record into database!<br/>";
			$insertSQL= sprintf("INSERT INTO visitor_daily_report (report_date, visits, unique_visitors, total_time) VALUES(%s, %s, %s, %s)",
						GetSQLValueString($index_date,"date"),
						GetSQLValueString($visits,"int"),
						GetSQLValueString($unique_visitors,"int"),
						GetSQLValueString($time,"int"));
			$result=mysql_query_or_die($insertSQL, $useradmin);
		}
	}

}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>用户统计数据</title>
<script language="javascript" type="text/javascript" src="../jquery/jquery-1.11.0.min.js"></script>
<script language="javascript" type="text/javascript" src="../jquery/jquery-ui-1.10.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../jqplot/jquery.jqplot.min.js"></script>
<script language="javascript" type="text/javascript" src="../jquery/jquery.json-2.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="../jqplot/plugins/jqplot.cursor.min.js"></script>
<script language="javascript" type="text/javascript" src="../jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="../jqplot/plugins/jqplot.highlighter.min.js"></script>
<script language="javascript" type="text/javascript" src="../jqplot/plugins/jqplot.pointLabels.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery/jquery-ui-1.10.4.css"/>
<link rel="stylesheet" type="text/css" href="../jqplot/jquery.jqplot.min.css"/>
<script type="text/javascript">
var plot1=null;
var line1=null;
var line2=null;

function createPlot(){
	var plot1 = $.jqplot('chartdiv', [line2, line1], {
		seriesColors:['#00749F', '#17BDB8'],
		series:[
				{xaxis:'xaxis',yaxis:'yaxis',label:"访问量"},
				{xaxis:'xaxis',yaxis:'yaxis',label:"访问次数"}],
		legend: {
			show:true,
			placement: 'outsideGrid'
		},
		title:'访问曲线图',
		axes:{
			xaxis:{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:{formatString:'%y/%#m/%#d'}
			},
			yaxis:{
				min:0,
				autoscale:true
			},
		},
		highlighter: {
			show: true,
			sizeAdjust: 10
		},
		cursor: {
			show: false
		}
	});
}
function reloadData(data1,data2){
	//console.log(data);
	if(data1!=null&&data2!=null){
		line1=data1;
		line2=data2;
		$('#chartdiv').empty();
		plot1=null;
		createPlot();
	}
}
$(document).ready(function(){
	var visits=convert_array(<?php echo json_encode($data_array['visits']);?>);
	var unique_visitors=convert_array(<?php echo json_encode($data_array['unique_visitors']);?>);
	reloadData(visits,unique_visitors);
	$('#startdate').datepicker();
	$('#enddate').datepicker();
});

function convert_array(json_array){
	var newData=[];
	for(var key in json_array){
		if(json_array.hasOwnProperty(key)){
			//newData+='['+key+','+return_data[key]+'],';
			newData.push([key,parseInt(json_array[key])]);
		}
	}
	return newData;
}
	
</script>
<style>
#chartdiv{
	
}
</style>
</head>

<body>
<h1>统计数据</h1>
<?php echo $today_visitor;?>
<div>
	开始: <input type="text" id="startdate">
	结束: <input type="text" id="enddate">
    <select id="func">
		<option value="">Time Interval</option>
		<option value="daily">Daily</option>
		<option value="weekly">Weekly</option>
	</select>
    <a id="go" href="javascript:get_result();">GO</a>
</div>


<div id="chartdiv" style="height:400px;width:800px; "></div>

</body>
<script type="text/javascript">
function get_result(){
	var start = $('#startdate').datepicker({ dateFormat: 'yyyy-mm-dd' }).val();
	var end = $('#enddate').datepicker({ dateFormat: 'yyyy-mm-dd' }).val();
	var func = $('#func').val();
	if(start.length>0&&end.length>0&&func.length>0){
		if(end>=start){
			var post_data=new Object();
			post_data['start']=start;
			post_data['end']=end;
			post_data['function']=func;
			$.post("server/get_report.php",post_data,function(){
				var return_data=arguments[0]['report'];
				var new_visits=convert_array(return_data['visits']);
				var new_unique_visitors=convert_array(return_data['unique_visitors']);
				
				reloadData(new_visits,new_unique_visitors);
			},"json");
		}else{
			alert("end date must be equal or greater than start date");
		}
	}else{
		if(func.length<=0){
			alert("You must specify time interval for the report");
		}
		if(start.length<=0||end.length<=0){
			alert("You must fill in both start and end date");
		}
		
	}
	
	
}

</script>
</html>