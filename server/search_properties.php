<?php 
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}


$keyword=isset($_POST['keyword'])?$_POST['keyword']:$_GET['keyword'];
$bedrooms=isset($_POST['bedrooms'])?$_POST['bedrooms']:$_GET['bedrooms'];
$bathrooms=isset($_POST['bathrooms'])?$_POST['bathrooms']:$_GET['bathrooms'];
$min_price=isset($_POST['min_price'])?$_POST['min_price']:$_GET['min_price'];
$max_price=isset($_POST['max_price'])?$_POST['max_price']:$_GET['max_price'];
$property_type=isset($_POST['property_type'])?$_POST['property_type']:$_GET['property_type'];
$filter='';
if(!empty($bedrooms)){
	switch($bedrooms){
		case 1: case 2: case 3:
		$filter.=' AND listings.bedrooms ='.$bedrooms;
		break;
		case 4:
		$filter.=' AND listings.bedrooms >='.$bedrooms;
		break;
	}
}
if(!empty($bathrooms)){
	switch($bathrooms){
		case 1: case 2:
		$filter.=" AND listings.bathrooms =".$bathrooms;
		break;
		case 3:
		$filter.=" AND listings.bathrooms >=".$bathrooms;
		break;
	}
}
if(!empty($property_type)){
	switch($property_type){
		case 'apartment': case 'condo':
		$filter.=" AND listings.type_of_dwelling='Apartment/Condo'";
		break;
		case 'multifamily':
		$filter.=" AND listings.type_of_dwelling='Townhouse' OR listings.type_of_dwelling='Apartment/Condo'";
		break;
	}
}
$selectSQL = "SELECT SQL_CALC_FOUND_ROWS DISTINCT * FROM listings LEFT JOIN listing_album ON listings.sysid = listing_album.sysid LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id LEFT JOIN photo_profile ON photo_profile.photo_id=album_profile.photo_id WHERE publish_on_internet='Y' AND status='A'".$filter;
$get_properties=mysql_query_or_die($selectSQL,$useradmin);
$row=mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()",$useradmin));
$properties_html_array=array();
while($row_get_properties=mysql_fetch_assoc($get_properties)){
		$image_url = $row_get_properties['photo_path'];
		$image_size = getimagesize($image_url);
		$type = $row_get_properties['type_of_dwelling'];
		$li_class=' '.str_replace('/',' ',$type);
		
		if(empty($image_size)||empty($image_size[0])||empty($image_size[1])){
			$image_url= 'img/imgdemo/300x180.gif';
		}
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
			  
	array_push($properties_html_array,$properties_html);
}
$return_array=array("rows"=>intval($row[0]),"html"=>$properties_html_array);
echo json_encode($return_array);
?>