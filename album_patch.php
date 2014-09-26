<?php
require_once 'dbconnect/dbconnect.php';
require_once 'utilities/utilities.php';
require_once 'RETS.php';
header('Content-Type: text/html; charset=utf-8');
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
$file="album.txt";
$current=file_get_contents($file);
$current.="$current_YVR_time_string\n";
file_put_contents($file,$current);

$deleteSQL = "DELETE FROM sysid_raw_recent_image_update WHERE sysid IN (SELECT sysid FROM listings WHERE date_updated_local>='".$before1hr_time_string."')";
$result = mysql_query_or_die($deleteSQL, $useradmin);

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sysid_raw_recent_image_update.sysid, sysid_raw_recent_image_update.num_of_photos FROM sysid_raw_recent_image_update".
			" LEFT JOIN listings ON sysid_raw_recent_image_update.sysid=listings.sysid".
			" WHERE !ISNULL(listings.sysid)".
			" ORDER BY RAND() LIMIT 0, 5";
$get_sysid = mysql_query_or_die($selectSQL, $useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
if($row[0]>0){
	$response=$rets->Login();
	echo "<br/>There are ".$row[0]." records in table sysid_raw_recent_image_update<br/>";
	//$response=$rets->Login();
	//var_dump($response);
	$i=0;$j=0;$k=0;$m=0;$empty=0;$no_photo_id=0;
	while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		$temp_sysid=$row_get_sysid['sysid'];
		$num_of_photos=$row_get_sysid['num_of_photos'];
		if(empty($num_of_photos)){
			$empty++;
			$deleteSQL=sprintf("DELETE FROM sysid_raw_recent_image_update WHERE sysid=%s",
											GetSQLValueString($temp_sysid,"int"));
			$result=mysql_query_or_die($deleteSQL, $useradmin);
			echo '<br/><span style="color:#ff0000;">'.$temp_sysid.' is removed from the database because remote data does\'t have num of photos data.</span><br/>';
		}else{
			$photo_id_array=get_photo_id($temp_sysid);
			
			if(count($photo_id_array)>0){
				if(count($photo_id_array)<$num_of_photos){
					//var_dump($photo_id_array);
					//echo "<br/>";
					$i++;
					echo $temp_sysid.":".$num_of_photos."<br/>";
					$album_id=check_and_insert($temp_sysid);
					if(!empty($album_id)){
					
					$response=$rets->GetPhoto('Property',$temp_sysid.':*', 'images');
					$photo_array=$response['photo_array'];
					//var_dump($photo_array);
					if(is_array($photo_array)&&count($photo_array)>=$num_of_photos){
						echo"<br/>Number of photos retrieved matches the database record.<br/>";
						for($i=0;$i<count($photo_array);$i++){
							$selectSQL="SELECT * FROM photo_profile WHERE photo_path='".$photo_array[$i]."'";
							//echo $selectSQL."<br/>";
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
							unset($selectSQL);
							
						}
						$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s WHERE sysid=%s",
											GetSQLValueString(count($photo_array),"int"),
											GetSQLValueString($temp_sysid,"int"));
						$result=mysql_query_or_die($updateSQL, $useradmin);
						$deleteSQL=sprintf("DELETE FROM sysid_raw_recent_image_update WHERE sysid=%s",
												GetSQLValueString($temp_sysid,"int"));
						$result=mysql_query_or_die($deleteSQL, $useradmin);
						echo  "<br/>$temp_sysid deleted from sysid_raw_recent_image_update.<br/>";
					}else{
						echo "<br/>Empty photo array or number of photos retrieved doesn't match the database record<br/>";
						$deleteSQL=sprintf("DELETE FROM sysid_raw_recent_image_update WHERE sysid=%s",
											GetSQLValueString($temp_sysid,"int"));
						$result=mysql_query_or_die($deleteSQL, $useradmin);
						echo '<br/><span style="color:#ff0000;">'.$temp_sysid.' is removed from the database because number of photos retrieved doesn\'t match the database record</span><br/>';
					}
					
				}
				}elseif(count($photo_id_array)>$num_of_photos){
					$j++;
					$deleteSQL=sprintf("DELETE FROM sysid_raw_recent_image_update WHERE sysid=%s",
											GetSQLValueString($temp_sysid,"int"));
					$result=mysql_query_or_die($deleteSQL, $useradmin);
					echo '<br/><span style="color:#ff0000;">'.$temp_sysid.' is removed from the database because it contains more photos than the remote data</span><br/>';
				}else{
					if(count($photo_id_array)==$num_of_photos){
						$m++;
						$deleteSQL=sprintf("DELETE FROM sysid_raw_recent_image_update WHERE sysid=%s",
											GetSQLValueString($temp_sysid,"int"));
						$result=mysql_query_or_die($deleteSQL, $useradmin);
						echo '<br/><span style="color:#ff0000;">'.$temp_sysid.' is removed from the database because it contains same amount of photos as the remote data</span><br/>';
						
					}
				}
			}else{
				$no_photo_id++;
				$album_id=check_and_insert($temp_sysid);
				if(!empty($album_id)){
					
					$response=$rets->GetPhoto('Property',$temp_sysid.':*', 'images');
					$photo_array=$response['photo_array'];
					for($i=0;$i<count($photo_array);$i++){
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
					$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s WHERE sysid=%s",
											GetSQLValueString(count($photo_array),"int"),
											GetSQLValueString($temp_sysid,"int"));
					$result=mysql_query_or_die($updateSQL, $useradmin);
					$deleteSQL=sprintf("DELETE FROM sysid_raw_recent_image_update WHERE sysid=%s",
											GetSQLValueString($temp_sysid,"int"));
					$result=mysql_query_or_die($deleteSQL, $useradmin);
					echo  "<br/>$temp_sysid deleted from sysid_raw_recent_image_update.<br/>";
				}
			}
		}
		//$num_of_photos_db=get_num_of_photos($row_get_sysid['sysid']);
		
	}
	echo "<br/>There are ".$i." records in database table that have less photos than remote data<br/>";
	
	echo "<br/>There are ".$j." records in database table that have more photos than remote data<br/>";
	
	echo "<br/>There are ".$empty." records remote data don't have num of photos<br/>";
	
	echo "<br/>There are ".$m." records in database table that have same photos as remote data<br/>";
	
	echo "<br/>There are ".$no_photo_id." records in database table that don't have local photo<br/>";
}

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sysid FROM listings 
				WHERE (ISNULL(listings.album_id) OR num_of_photos<=1) 
					AND album_patched='N' 
					AND status='A' 
					AND property_type!='Land Only' 
					AND area_desc!='VOT' 
					AND area_desc!='FOT' 
				ORDER BY RAND() LIMIT 0, 10";
$get_sysid=mysql_query_or_die($selectSQL, $useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));


//login and receive server response.
if(is_array($row)&&$row[0]>0){
	echo '<br/>'.$row[0].'records needs to be album_patched.<br/>';
	$response=$rets->Login();
	patch_album($get_sysid);
	/*while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		echo '<br/><br/>checking:<span style="color:#00ff00;">'.$row_get_sysid['sysid'].'</span><br/>';
		$album_id=check_and_insert($row_get_sysid['sysid']);
		if(!empty($album_id)){
			$photo_id_array=get_photo_id($row_get_sysid['sysid']);
			var_dump($photo_id_array);
			$response=$rets->GetPhoto('Property',$row_get_sysid['sysid'].':*', 'images');
			$photo_array=$response['photo_array'];
			//var_dump($photo_array);
			echo "<br/>服务器图片数量：".count($photo_array).' & 本地图片数量：'.count($photo_id_array)."<br/>";
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
						//echo $selectSQL;
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
					unset($selectSQL);
				}
				$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_array),"int"),
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
				$result=mysql_query_or_die($updateSQL, $useradmin);				
			}else{
				if(count($photo_id_array)==count($photo_array)){
					if(count($photo_array)==1){
						$updateSQL=sprintf("UPDATE listings SET album_patched=%s WHERE sysid=%s",
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
						$result=mysql_query_or_die($updateSQL, $useradmin);	
						echo "<br/>retrieved 1 photo, and it is already in local database<br/>";
					}else{
						echo "<br/><span style=\"color:green;\">retrieved more than 1 photo, and it is already in local database, updating num_of_photos in listings table</span><br/>";
						$updateSQL = sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_array),"int"),
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
						$result=mysql_query_or_die($updateSQL, $useradmin);	
									
					}
				}else{
					echo "<br/><span style=\"color:green;\">取得的图片[".count($photo_array)."]没有本地图片[".count($photo_id_array)."]多</span><br/>";
					$updateSQL = sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_id_array),"int"),
									GetSQLValueString(count($photo_array)==0?'Y':'Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
					$result=mysql_query_or_die($updateSQL, $useradmin);	
	
				}
				
				
			}
		}
	}*/
}else{
	echo "Empty Set<br/>";
}

$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT sysid FROM listings LEFT JOIN album_profile ON listings.album_id=album_profile.album_id LEFT JOIN photo_profile ON album_profile.photo_id=photo_profile.photo_id WHERE photo_path='http://80a5e3a6041af90156b0-bf53eb49f254872ee431fd1273bc084d.r45.cf2.rackcdn.com/_.jpg' AND status='A' AND property_type!='Land Only' AND area_desc!='VOT' AND area_desc!='FOT' AND city!='No City Value' ORDER BY RAND() LIMIT 0, 5";
$get_sysid=mysql_query_or_die($selectSQL, $useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));

//login and receive server response.
if(is_array($row)&&$row[0]>0){
	echo '<br/>'.$row[0].'records needs to be album_patched.<br/>';
	$response=$rets->Login();
	patch_album($get_sysid);
	/*while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		echo '<br/><br/>checking:<span style="color:#00ff00;">'.$row_get_sysid['sysid'].'</span><br/>';
		$album_id=check_and_insert($row_get_sysid['sysid']);
		if(!empty($album_id)){
			$photo_id_array=get_photo_id($row_get_sysid['sysid']);
			var_dump($photo_id_array);
			$response=$rets->GetPhoto('Property',$row_get_sysid['sysid'].':*', 'images');
			$photo_array=$response['photo_array'];
			//var_dump($photo_array);
			echo "<br/>服务器图片数量：".count($photo_array).' & 本地图片数量：'.count($photo_id_array)."<br/>";
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
						//echo $selectSQL;
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
					unset($selectSQL);
				}
				$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_array),"int"),
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
				$result=mysql_query_or_die($updateSQL, $useradmin);				
			}else{
				if(count($photo_id_array)==count($photo_array)){
					if(count($photo_array)==1){
						$updateSQL=sprintf("UPDATE listings SET album_patched=%s WHERE sysid=%s",
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
						$result=mysql_query_or_die($updateSQL, $useradmin);	
						echo "<br/>retrieved 1 photo, and it is already in local database<br/>";
					}else{
						echo "<br/><span style=\"color:green;\">retrieved more than 1 photo, and it is already in local database, updating num_of_photos in listings table</span><br/>";
						$updateSQL = sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_array),"int"),
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
						$result=mysql_query_or_die($updateSQL, $useradmin);	
									
					}
				}else{
					echo "<br/><span style=\"color:green;\">取得的图片[".count($photo_array)."]没有本地图片[".count($photo_id_array)."]多</span><br/>";
					$updateSQL = sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_id_array),"int"),
									GetSQLValueString(count($photo_array)==0?'Y':'Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
					$result=mysql_query_or_die($updateSQL, $useradmin);	
	
				}
				
				
			}
		}
	}*/
}else{
	echo "Empty Set<br/>";
}

function patch_album($get_sysid){
	global $rets, $useradmin;
	while($row_get_sysid=mysql_fetch_assoc($get_sysid)){
		echo '<br/><br/>checking:<span style="color:#00ff00;">'.$row_get_sysid['sysid'].'</span><br/>';
		$album_id=check_and_insert($row_get_sysid['sysid']);
		if(!empty($album_id)){
			$photo_id_array = get_photo_id($row_get_sysid['sysid']);
			echo "<br/>";
			var_dump($photo_id_array);
			echo "<br/>";
			$response=$rets->GetPhoto('Property',$row_get_sysid['sysid'].':*', 'images');
			$photo_array=$response['photo_array'];
			echo "<br/>服务器图片数量：".count($photo_array).' & 本地图片数量：'.count($photo_id_array)."<br/>";
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
						//echo $selectSQL;
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
					unset($selectSQL);
				}
				$updateSQL=sprintf("UPDATE listings SET num_of_photos=%s, album_patched=%s WHERE sysid=%s",
									GetSQLValueString(count($photo_array),"int"),
									GetSQLValueString('Y',"text"),
									GetSQLValueString($row_get_sysid['sysid'],"int"));
				$result=mysql_query_or_die($updateSQL, $useradmin);	
			}elseif(count($photo_id_array)==count($photo_array)){
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
									GetSQLValueString($row_get_sysid['sysid'],"int"));
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
	}
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
	$selectSQL = "SELECT num_of_photos FROM listings WHERE sysid=".$sysid;
	$get_num_of_photos = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_num_of_photos=mysql_fetch_assoc($get_num_of_photos)){
		return $row_get_num_of_photos['num_of_photos'];
	}else{
		return false;
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
