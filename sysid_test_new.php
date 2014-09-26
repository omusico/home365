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


//login and receive server response.
$response=$rets->Login();
var_dump($response);


//The following code is to test if the cron job is executed.
$file="file.txt";
$current=file_get_contents($file);
$current.="$current_YVR_time_string from sysid_test.php\n";
file_put_contents($file,$current);

$sysid_array=$rets->GetDataArray('Property','11','(363=|A)', 'sysid', null);
//var_dump($sysid_array);
$truncateSQL = "TRUNCATE TABLE sysid_raw_current";
$result=mysql_query_or_die($truncateSQL, $useradmin);
foreach($sysid_array as $index=>$array){
	insert_sysid($array['sysid']);
}

function insert_sysid($sysid){
	global $useradmin;
	$selectSQL = "SELECT * FROM sysid_raw_current WHERE sysid=$sysid";
	$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_sysid=mysql_fetch_assoc($get_sysid)){
	}else{
		$insertSQL = sprintf("INSERT INTO sysid_raw_current (sysid) VALUES(%s)",
		GetSQLValueString($sysid,"int"));
		$result = mysql_query_or_die($insertSQL, $useradmin);
	}
}


?>