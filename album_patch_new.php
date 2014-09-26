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
$before1hr_time_string = date("Y-m-d H:i:s",$current_YVR_time-3600); 

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sysid FROM listings WHERE (ISNULL(listings.album_id) OR num_of_photos<=1) AND status='A' AND property_type!='Land Only' ORDER BY RAND() LIMIT 0, 10";
$get_sysid=mysql_query_or_die($selectSQL, $useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));

//login and receive server response.
if(is_array($row)&&$row[0]>0){
	echo '<br/>'.$row[0].'records needs to be album_patched.<br/>';
	$response=$rets->Login();
	
	while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		echo '<br/><br/>checking:<span style="color:#00ff00;">'.$row_get_sysid['sysid'].'</span><br/>';
		$album_id=check_and_insert($row_get_sysid['sysid']);
		if(!empty($album_id)){
			$photo_id_array=get_photo_id($row_get_sysid['sysid']);
			var_dump($photo_id_array);
			$response=$rets->GetPhoto('Property',$row_get_sysid['sysid'].':*', 'images');
			$photo_array=$response['photo_array'];
			if(count($photo_array)>count($photo_id_array)){
				for($i=0;$i<count($photo_array);$i++){
					$selectSQL="SELECT * FROM photo_profile WHERE photo_path='".$photo_array[$i]."'";
					
					if($photo_item=mysql_fetch_assoc(mysql_query_or_die($selectSQL, $useradmin))){
						echo "<br/>photo_path found in database, nothing to be inserted.<br/>";
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
					unset($selectSQL);
				}
				$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_array),"int"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
				$result=mysql_query_or_die($updateSQL, $useradmin);				
			}else{
			}
		}
	}
}else{
	echo "Empty Set<br/>";
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
function get_num_of_photos($sysid){
	global $useradmin;
	/*$selectSQL="SELECT SQL_CALC_FOUND_ROWS listings.sysid, listings.album_id, listing_album.album_id, album_profile.photo_id, photo_profile.photo_id, photo_path FROM listings".
		" LEFT JOIN listing_album ON listings.sysid=listing_album.sysid".
		" LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id".
		" LEFT JOIN photo_profile ON album_profile.photo_id=photo_profile.photo_id".
		" WHERE listings.sysid=$sysid AND !ISNULL(photo_path) GROUP BY photo_path";*/
	$selectSQL = "SELECT num_of_photos FROM listings WHERE sysid=".$sysid;
	$get_num_of_photos = mysql_query_or_die($selectSQL, $useradmin);
	//$row = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	if($row_get_num_of_photos=mysql_fetch_assoc($get_num_of_photos)){
		return $row_get_num_of_photos['num_of_photos'];
	}else{
		return false;
	}
	/*if(is_array($row)&&!empty($row)){
		return $row[0];
	}else{
		return false;
	}*/
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
