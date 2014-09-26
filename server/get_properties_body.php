<?php 
//require_once 'get_properties.php';
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
function get_properties_summary($from, $size, $theme){
	global $useradmin,$document_root;
	$properties_html_array=array();
	$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT * FROM listings LEFT JOIN listing_album ON listings.sysid = listing_album.sysid LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id LEFT JOIN photo_profile ON photo_profile.photo_id=album_profile.photo_id WHERE status='A' AND publish_on_internet='Y' AND album_profile.cover='Y' ORDER BY list_date DESC LIMIT $from, $size ";
	$get_properties=mysql_query_or_die($selectSQL,$useradmin);
	$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
	while($row_get_properties=mysql_fetch_assoc($get_properties)){
		//determing if photo is corrupted 
		$image_url = $row_get_properties['photo_path'];
		$image_size = getimagesize($image_url);
		$type = $row_get_properties['type_of_dwelling'];
		$li_class=' '.str_replace('/',' ',$type);
		
		if(empty($image_size)||empty($image_size[0])||empty($image_size[1])){
			$image_url= 'img/imgdemo/300x180.gif';
		}
		if($theme=='home'){
			$properties_html='<li style="display: block;" class="span4'.$li_class.'">';
				$properties_html.='<div class="product-item">';
					$properties_html.='<div class="imagewrapper">';
						$properties_html.='<img alt="" width="300" height="180" src="'.$image_url.'">';
						//$size=getimagesize($document_root.'/images/'.$row_get_properties['sysid'].'_1.jpg">');
						$properties_html.='<span class="price"> $'.number_format($row_get_properties['list_price']).'</span>';
					$properties_html.='</div>';
					$properties_html.='<h3><a href="property_detail.php?sysid='.$row_get_properties['sysid'].'" title="">'.$row_get_properties['address'].'</a></h3>';
					$properties_html.='<ul class="title-info">';
						$properties_html.='<li>Bedrooms 睡房<span> '.$row_get_properties['bedrooms'].'</span></li>';
						$properties_html.='<li>Bathrooms 卫生间<span> '.$row_get_properties['bathrooms'].'</span></li>';
						
						$properties_html.='<li>Square Footage 套内面积<span>'.$row_get_properties['floor_area_total'].' sqft</span></li>';
						$properties_html.='<li>Type 房型 <span>'.$row_get_properties['type_of_dwelling'].'</span></li>';
					$properties_html.='</ul>';
				$properties_html.='</div>';
			$properties_html.='</li>';
		}elseif($theme=='list'){
			$properties_html='<li style="display: block;" class="span12">';
                $properties_html.='<div class="product-item">';
                  $properties_html.='<div class="row">';
                    $properties_html.='<div class="span4">';
                      $properties_html.='<div class="imagewrapper">';
                        $properties_html.='<img alt="" width="300" height="180" src="'.$image_url.'">';
                        $properties_html.='<span class="price"> $'.number_format($row_get_properties['list_price']).'</span>';
                      $properties_html.='</div>';
                    $properties_html.='</div>';
                    $properties_html.='<div class="span8">';
                      $properties_html.='<div class="list-right-info">';
                        $properties_html.='<div class="row">';
                          $properties_html.='<div class="span4">';
                            $properties_html.='<h3><a href="property_detail.php?sysid='.$row_get_properties['sysid'].'" title="">'.$row_get_properties['address'].'</a></h3>';
                            $properties_html.='<p>';
                              $proerties_html.=$row_get_properties['public_remarks'];
                            $properties_html.='</p>';
                          $properties_html.='</div>';
                          $properties_html.='<div class="span4">';
                            $properties_html.='<ul class="title-info">';
                              $properties_html.='<li>Bedrooms 睡房<span> '.$row_get_properties['bedrooms'].'</span> </li>';
                              $properties_html.='<li>Bathrooms 卫生间<span> '.$row_get_properties['bathrooms'].'</span></li>';
                              $properties_html.='<li>Square Footage 套内面积<span>'.$row_get_properties['floor_area_total'].' sqft</span></li>';
                              $properties_html.='<li>Type 房型<span>'.$row_get_properties['type_of_dwelling'].'</span></li>';
                            $properties_html.='</ul>';
                          $properties_html.='</div>';
                        $properties_html.='</div>';
                      $properties_html.='</div>';
                    $properties_html.='</div>';
                  $properties_html.='</div>';
                $properties_html.='</div>';
              $properties_html.='</li>';
		}
		array_push($properties_html_array,$properties_html);
	}
	$return_array=array("rows"=>intval($row[0]),"html"=>$properties_html_array);
	return $return_array;	
}
?>