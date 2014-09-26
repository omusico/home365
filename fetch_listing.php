<?php 
error_reporting(E_ALL);
$document_root='/home/home365/public_html';
$domain_name='http://www.home365.ca/';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
require_once $document_root.'/RETS.php';

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
$current_YVR_date=date("Y-m-d",$current_YVR_time);

$mls=$_POST['mls'];
$response=$rets->Login();
$response=$rets->GetDataArray('Property','11','(248='.$mls.')','sysid,248,363,217',2);
$temp_sysid=$response[0]['sysid'];
$selectSQL = "SELECT * FROM listings WHERE sysid=$temp_sysid";
$get_listing = mysql_query_or_die($selectSQL, $useradmin);
if($listing_detail=mysql_fetch_assoc($get_listing)){
	//print_r($listing_detail);
	if($listing_detail['num_of_photos']<=1){
		echo "<br/><span style=\"color:red\">该房产在数据库没有图片（或只有一张图片）</span><br/>";
	}else{
		echo "<br/><span style=\"color:green;\">该房产共有".$listing_detail['num_of_photos']."张照片</span><br/>";
	}
	$album_id=check_and_insert($listing_detail['sysid']);
	$response=$rets->GetPhoto('Property',$listing_detail['sysid'].':*', 'images');
	$photo_array=$response['photo_array'];
	$photo_id_array = get_photo_id($listing_detail['sysid']);
	if(count($photo_array)>count($photo_id_array)){
		echo "<br/>We got more photos from the remote server<br/>";
		for($i=0;$i<count($photo_array);$i++){
			$selectSQL="SELECT * FROM photo_profile WHERE photo_path='".$photo_array[$i]."'";
			if($photo_item=mysql_fetch_assoc(mysql_query_or_die($selectSQL, $useradmin))){
				echo "<br/>photo_path found in database, nothing to be inserted.<br/>";
			}else{
				$selectSQL = "SELECT photo_profile.photo_id FROM listings LEFT JOIN album_profile ON listings.album_id = album_profile.album_id".
					" LEFT JOIN photo_profile ON album_profile.photo_id = photo_profile.photo_id".
					" WHERE photo_path='http://80a5e3a6041af90156b0-bf53eb49f254872ee431fd1273bc084d.r45.cf2.rackcdn.com/_.jpg'".
					" AND listings.album_id=$album_id";
				$get_photo = mysql_query_or_die($selectSQL, $useradmin);
				if($row_get_photo=mysql_fetch_assoc($get_photo)){
					echo "<br/>Broken image found. Updating record.<br/>";
					$photo_id=$row_get_photo['photo_id'];
					$photo_path=$photo_array[$i];
					$update_SQL=sprintf("UPDATE photo_profile SET photo_path=%s WHERE photo_id=%s",
							GetSQLValueString($photo_path,"text"),
							GetSQLValueString($photo_id,"int"));
					$result=mysql_query_or_die($update_SQL, $useradmin);
				}else{
					echo "<br/>photo_path not found in database, inserting new record<br/>";
					$insertPhotoSQL = sprintf("INSERT INTO photo_profile(photo_path)VALUES(%s)",
							GetSQLValueString($photo_array[$i],"text"));
					$result=mysql_query_or_die($insertPhotoSQL,$useradmin);
					$photo_id=mysql_insert_id($useradmin);
					$insertAlbumSQL = sprintf("INSERT INTO album_profile(album_id, photo_id, cover)VALUES(%s,%s,%s)",
							GetSQLValueString($album_id,"int"),
							GetSQLValueString($photo_id,"int"),
							GetSQLValueString($i==0?'Y':'N',"text"));
					$result=mysql_query_or_die($insertAlbumSQL,$useradmin);
					}
				}
		}
		$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
							GetSQLValueString(count($photo_array),"int"),
							GetSQLValueString('Y',"text"),
							GetSQLValueString($listing_detail['sysid'],"int"));
		$result=mysql_query_or_die($updateSQL, $useradmin);	
	}elseif(count($photo_array)==count($photo_id_array)){
		for($i=0;$i<count($photo_array);$i++){
			//first we have to check if local database image is broken
			$selectSQL = "SELECT photo_profile.photo_id FROM listings LEFT JOIN album_profile ON listings.album_id = album_profile.album_id".
							" LEFT JOIN photo_profile ON album_profile.photo_id = photo_profile.photo_id".
							" WHERE photo_path='http://80a5e3a6041af90156b0-bf53eb49f254872ee431fd1273bc084d.r45.cf2.rackcdn.com/_.jpg'".
							" AND listings.album_id=$album_id";
							//echo $selectSQL;
			$get_photo = mysql_query_or_die($selectSQL, $useradmin);
			if($row_get_photo=mysql_fetch_assoc($get_photo)){
				echo "<br/><span style=\"color:red\">There is a broken image in album $album_id. Updating record.</span><br/>";
				$photo_id=$row_get_photo['photo_id'];
				$photo_path=$photo_array[$i];
				$update_SQL=sprintf("UPDATE photo_profile SET photo_path=%s WHERE photo_id=%s",
									GetSQLValueString($photo_path,"text"),
									GetSQLValueString($photo_id,"int"));
				$result=mysql_query_or_die($update_SQL, $useradmin);
			}
		}
		$updateSQL=sprintf("UPDATE listings SET album_patched=%s WHERE sysid=%s",
						GetSQLValueString('Y',"text"),
						GetSQLValueString($listing_detail['sysid'],"int"));
		$result=mysql_query_or_die($updateSQL, $useradmin);	
	}else{
		
		echo "<br/><span style=\"color:green;\">取得的图片[".count($photo_array)."]没有本地图片[".count($photo_id_array)."]多</span><br/>";
		$selectSQL = "SELECT photo_profile.photo_id FROM listings LEFT JOIN album_profile ON listings.album_id = album_profile.album_id".
						" LEFT JOIN photo_profile ON album_profile.photo_id = photo_profile.photo_id".
						" WHERE photo_path='http://80a5e3a6041af90156b0-bf53eb49f254872ee431fd1273bc084d.r45.cf2.rackcdn.com/_.jpg'".
						" AND listings.album_id=$album_id";
						//echo $selectSQL;
		$get_photo = mysql_query_or_die($selectSQL, $useradmin);
		if($row_get_photo=mysql_fetch_assoc($get_photo)){
			echo "<br/>Broken image found. Updating record.<br/>";
			$photo_id=$row_get_photo['photo_id'];
			$deleteSQL = "DELETE FROM album_profile WHERE photo_id=$photo_id";
			$result = mysql_query_or_die($deleteSQL, $useradmin);
			$photo_id_array=get_photo_id($row_get_sysid['sysid']);
			$updateSQL = sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
							GetSQLValueString(count($photo_id_array),"int"),
							GetSQLValueString(count($photo_array)==0?'Y':'Y',"text"),
							GetSQLValueString($row_get_sysid['sysid'],"int"));
			$result=mysql_query_or_die($updateSQL, $useradmin);	
		}
			
	}
}
$geo_address = get_geoaddress($listing_detail['sysid']);

if(empty($geo_address['lat'])){
	echo "<br/><span style=\"color:red\">该房产在数据库没有地理坐标信息</span><br/>";
}else{
	echo "<br/><span style=\"color:green;\">该房产的地理位置为[LAT:".$geo_address['lat']."] [LNT:".$geo_address['lng']."]</span><br/>";
}



function get_geoaddress($sysid){
	global $useradmin;
	$selectSQL = "SELECT lat, lng FROM listing_geoaddress WHERE sysid=$sysid";
	$result = mysql_fetch_assoc(mysql_query_or_die($selectSQL, $useradmin));
	return $result;
}
function check_and_insert($sysid){
	global $useradmin;
	$selectSQL = "SELECT * FROM listing_album WHERE sysid=$sysid";
	$get_album = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_album=mysql_fetch_assoc($get_album)){
		echo '<br/><span style="color:#ff0000">album found in database</span><br/>';
		echo '<br/><span style="color:#ff0000">returning'.$row_get_album['album_id'].' as album_id</span><br/>';
		return $row_get_album['album_id'];
	}else{
		$insertSQL=sprintf("INSERT INTO listing_album (sysid) VALUES(%s)",
						GetSQLValueString($sysid,"int"));
		$result=mysql_query_or_die($insertSQL,$useradmin);
		$album_id=mysql_insert_id($useradmin);
		$updateSQL=sprintf("UPDATE listings SET album_id=%s WHERE sysid=%s",
						GetSQLValueString($album_id,"int"),
						GetSQLValueString($sysid,"int"));
		$result=mysql_query_or_die($updateSQL,$useradmin);
		return $album_id;
	}
}
function get_photo_id($sysid){
	global $useradmin;
	$selectSQL = "SELECT album_profile.photo_id FROM listing_album".
				" LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id".
				" LEFT JOIN photo_profile ON album_profile.photo_id=photo_profile.photo_id".
				" WHERE !ISNULL(photo_profile.photo_id) AND !ISNULL(photo_profile.photo_path) AND listing_album.sysid=".$sysid." GROUP BY photo_path";
	$get_photo_id = mysql_query_or_die($selectSQL, $useradmin);
	$photo_id_array=array();
	while($row_get_photo_id=mysql_fetch_assoc($get_photo_id)){
		array_push($photo_id_array, $row_get_photo_id['photo_id']);
	}
	return $photo_id_array;
}
?>