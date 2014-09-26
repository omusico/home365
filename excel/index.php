<?php 
ini_set("memory_limit","512M"); 
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
require_once 'excel_reader.php';

if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}

function null_2_default($string,$default) {
	return (isset($string)&&$string!=''?$string:$default);
}
$current_timestamp = get_GMT(microtime(true));
$current_time = date("Y-m-d H:i:s",$current_timestamp);
$data = new Spreadsheet_Excel_Reader("mls.xls");
$sheet_count=$data->sheetcount();

for($sheet_index=0; $sheet_index<$sheet_count; $sheet_index++){
	$row_count = $data->rowcount($sheet_index);
	$col_count = $data->colcount($sheet_index);
	echo "Sheet:$sheet_index<br/>";
	echo "Row Count:".$row_count."<br/>";
	echo "Column Count:".$col_count."<br/>";
	for($row_index=1; $row_index<=$row_count; $row_index++){
		for($col_index=1; $col_index<=$col_count; $col_index++){
			if($row_index>1&&$col_index<=3){
				if($col_index==1){
					$month=($data->val($row_index,$col_index,$sheet_index)===''?$month:$data->val($row_index,$col_index,$sheet_index));
				}
				if($col_index==2){
					$year=($data->val($row_index,$col_index,$sheet_index)===''?$year:$data->val($row_index,$col_index,$sheet_index));
				}
				if($col_index==3){
					$property_type=($data->val($row_index,$col_index,$sheet_index)===''?$property_type:$data->val($row_index,$col_index,$sheet_index));
				}
				
			}else{
				echo "<td>".$data->val($row_index,$col_index,$sheet_index).'</td>';
			}
		}
		echo "</tr>";
	}
	echo "</table>";
}
?>