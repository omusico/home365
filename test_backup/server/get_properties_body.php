<?php 
//require_once '../../test1/server/get_properties.php';
function get_properties_summary($from, $size){
	global $useradmin,$document_root;
	$properties_html_array=array();
	$selectSQL = "SELECT * FROM listings WHERE status='A' AND publish_on_internet='Y' ORDER BY list_date DESC LIMIT $from, $size ";
	$get_properties=mysql_query_or_die($selectSQL,$useradmin);
	while($row_get_properties=mysql_fetch_assoc($get_properties)){
		//determing if photo is corrupted 
		$image_url = 'http://www.'.$_SERVER['SERVER_NAME'].'/images/'.$row_get_properties['sysid'].'_1.jpg';
		$image_size=getimagesize($image_url);
		if(empty($image_size)||empty($image_size[0])||empty($image_size[1])){
			$image_url= 'img/imgdemo/300x180.gif';
		}
		$properties_html='<li style="display: block;" class="span4">';
			$properties_html.='<div class="product-item">';
				$properties_html.='<div class="imagewrapper">';
					$properties_html.='<img alt="" width="300" height="180" src="'.$image_url.'">';
					//$size=getimagesize($document_root.'/images/'.$row_get_properties['sysid'].'_1.jpg">');
					$properties_html.='<span class="price"> $'.number_format($row_get_properties['list_price']).'</span>';
				$properties_html.='</div>';
				$properties_html.='<h3><a href="property_detail.php?sysid='.$row_get_properties['sysid'].'" title="">'.$row_get_properties['address'].'</a></h3>';
				$properties_html.='<ul class="title-info">';
					$properties_html.='<li>Bathrooms <span> '.$row_get_properties['bathrooms'].'</span></li>';
					$properties_html.='<li>Bathrooms <span> '.$row_get_properties['bedrooms'].'</span></li>';
					$properties_html.='<li>Square Footage <span>'.$row_get_properties['floor_area_total'].' sqft</span></li>';
					$properties_html.='<li>Type: <span>'.$row_get_properties['type_of_dwelling'].'</span></li>';
				$properties_html.='</ul>';
			$properties_html.='</div>';
		$properties_html.='</li>';
		array_push($properties_html_array,$properties_html);
	}
	return $properties_html_array;	
}
?>