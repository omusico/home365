<?php 
$document_root='/home/home365/public_html';
require_once $document_root.'/dbconnect/dbconnect.php';
require_once $document_root.'/utilities/utilities.php';
if(mysql_select_db("home365_ios",$useradmin)){
}else{
	echo "Error selecting database, exited.";
	exit();
}
$sysid=isset($_GET['sysid'])?$_GET['sysid']:$_POST['sysid'];
if(!empty($sysid)){
	$selectSQL = "SELECT * FROM listings".
	" LEFT JOIN listing_realtors ON listings.sysid=listing_realtors.sysid".
	" LEFT JOIN listing_firms ON listings.sysid=listing_firms.sysid".
	" WHERE publish_on_internet='Y' AND status='A' AND listings.sysid=".$sysid;
	$get_property=mysql_query_or_die($selectSQL, $useradmin);
	$propertyObject=array();
	if($propertyObject=mysql_fetch_assoc($get_property)){
		$photo_array = get_album($sysid);
		$realtor_array = array($propertyObject['list_realtor_1_id'],$propertyObject['list_realtor_2_id'],$propertyObject['list_realtor_3_id']);
		$firm_array = array($propertyObject['list_firm_1_code'],$propertyObject['list_firm_2_code']);
		$realtor_profile_array=array();
		$firm_profile_array=array();
		foreach($realtor_array as $realtor_id){
			$realtor_profile=get_realtor_profile($realtor_id);
			array_push($realtor_profile_array, $realtor_profile);
		}
		foreach($firm_array as $firm_code){
			$firm_profile = get_firm_profile($firm_code);
			array_push($firm_profile_array, $firm_profile);
		}
		//var_dump($realtor_profile_array);
		//var_dump($firm_profile_array);
	}else{
		echo "Error: The property you are looking for is no longer active";
		exit();
	}
	$address=$propertyObject['address'].' '.$propertyObject['city'].' '.$propertyObject['province'];
	$geo_address=get_geocode($address,$sysid);
}
function get_album($sysid){
	global $useradmin;
	$selectAlbumSQL = "SELECT * FROM listing_album".
					" LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id".
					" LEFT JOIN photo_profile ON photo_profile.photo_id=album_profile.photo_id".
					" WHERE listing_album.sysid=".$sysid;
	$get_album=mysql_query_or_die($selectAlbumSQL, $useradmin);
	$photo_array=array();
	while ($row_get_album=mysql_fetch_assoc($get_album)){
		array_push($photo_array, $row_get_album['photo_path']);
	}
	//var_dump($photo_array);
	return $photo_array;
		
}
function get_geocode($address, $sysid){
	global $useradmin;
	$selectSQL = "SELECT * FROM listing_geoaddress WHERE sysid=".$sysid;
	$get_geocode=mysql_query_or_die($selectSQL,$useradmin);
	if($row_get_geocode=mysql_fetch_assoc($get_geocode)){
		return $row_get_geocode;
	}else{
		$url="http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($address);
		$resp_json = file_get_contents($url);
		$resp = json_decode($resp_json, true);
		if($resp['status']='OK'){
			$insertSQL = sprintf("INSERT INTO listing_geoaddress(sysid, lat, lng)VALUES(%s,%s,%s)",
						GetSQLValueString($sysid,"int"),
						GetSQLValueString($resp['results'][0]['geometry']['location']['lat'],"double"),
						GetSQLValueString($resp['results'][0]['geometry']['location']['lng'],"double"));
			$result=mysql_query_or_die($insertSQL,$useradmin);
			return $resp['results'][0]['geometry']['location'];
		}else{
			return false;
		}
	}
}
function get_realtor_profile($realtor_id){
	global $useradmin;
	$selectSQL = "SELECT * FROM realtor_profile WHERE realtor_id='".$realtor_id."'";
	$get_realtor = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_realtor=mysql_fetch_assoc($get_realtor)){
		return $row_get_realtor;
	}else{
		return false;
	}
}
function get_firm_profile($firm_code){
	global $useradmin;
	$selectSQL = "SELECT * FROM firm_profile WHERE firm_code='".$firm_code."'";
	$get_firm = mysql_query_or_die($selectSQL, $useradmin);
	if($row_get_firm=mysql_fetch_assoc($get_firm)){
		return $row_get_firm;
	}else{
		return false;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo $propertyObject['address'];?> | Allison Jiang's Real Estate Site | 地产知道 | 大温地产专业网站</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <!-- Your styles -->
  <link href="../test1/css/bootstrap.css" rel="stylesheet" media="screen">
  <link href="../test1/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
  <link href="../test1/css/flexslider/flexslider.css" rel="stylesheet" media="screen">
  <link href="../test1/css/tabber/tabber.css" rel="stylesheet" media="screen">
  <link href="../test1/css/colorbox/colorbox.css" rel="stylesheet" media="screen">
  <link href="css/styles.css" rel="stylesheet" media="screen">
  <link href="../test1/css/responsive.css" rel="stylesheet" media="screen">
  <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Raleway:400,700,100,200,300' rel='stylesheet' type='text/css'>
  <!-- HTML5 shim, for Ie6-8 support of HTML5 elements -->
    <!--[if lt Ie 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <!-- BEGIN HEADER -->
    <header>
    <?php require_once'../test1/html/header.php'; ?>
   <!-- Bannber -->
   <div class="row-fluid">
    <div class="span12">
      <section class="pic-cat">
        <img width="1900" height="200" alt="" src="../test1/img/imgdemo/1900-200.png">
      </section>
    </div>
  </div>
  <!-- Banner -->
</header>
<!-- END HEADER -->

<!-- BEGIN CONTENT -->
<div class="main-content">
  <div class="properties">
    <div class="container">
      <div class="grid_full_width gird_sidebar">
        <div class="row">
         
         <!-- Main content -->
         <div class="span8">
           <!-- Property detail -->
           <div class="property_detail">
            <section class="slider-detail">
              <div id="pic-detail" class="flexslider">
                <ul class="slides">
				<?php 
					if(count($photo_array)==0){
						$slide_html='<li>';
						$slide_html.='<a class="detailbox" href="img/imgdemo/620x388.gif"title="'.$propertyObject['address'].'"><img alt="" width="620" height="388" src="img/imgdemo/620x388.gif"/></a>';
						$slide_html.='</li>';
					}else{
						$slide_html='';
						for($i=0;$i<count($photo_array);$i++){
							$image_url = $photo_array[$i];
							if($i==0){
								$image_size=getimagesize($image_url);
								if(empty($image_size)||empty($image_size[0])||empty($image_size[1])){
									$image_url= 'img/imgdemo/620x388.gif';
								}
							}
							$slide_html.='<li>';
							$slide_html.='<a class="detailbox" href="'.$image_url.'"title="'.$propertyObject['address'].'"><img alt="" width="620" height="388" src="'.$image_url.'"/></a>';
							$slide_html.='</li>';
							
						}
					}
					echo $slide_html;
				?>
                  <!--<li>
                    <a class="detailbox" href="img/imgdemo/620x388.gif" title="1524A consectetur purus sit amet fermentum."><img alt=""  width="620" height="388" src="img/imgdemo/620x388.gif" /></a>
                  </li>
                  -->
                  
                </ul>
              </div>
              <div id="pic-control" class="flexslider">
                <ul id="pic-nav" class="slides">
				<?php 
					if(count($photo_array)<=1){
						$slide_html='';
						//$slide_html ='<li>';
						//$slide_html.='<img alt="" width="55" src="img/imgdemo/620x388.gif"/>';
						//$slide_html.='</li>';
					}else{
						$slide_html='';
						for($i=0;$i<count($photo_array);$i++){
							$image_url = $photo_array[$i];
							if($i==0){
								$image_size=getimagesize($image_url);
								if(empty($image_size)||empty($image_size[0])||empty($image_size[1])){
									$image_url= 'img/imgdemo/620x388.gif';
								}
							}
							$slide_html.='<li>';
							$slide_html.='<img alt="" src="'.$image_url.'"/></a>';
							$slide_html.='</li>';
							
						}
					}
					echo $slide_html;
				?>
                  
                  
                
                  
                </ul>
              </div>
            </section>
            <div class="infotext-detail">
              <h3><?php echo $propertyObject['address'];?></h3>
              <span class="price">$<?php echo number_format($propertyObject['list_price']);?></span>
              <div class="row">
                <div class="span260px">
                  <ul class="title-info">
                    
                    <li>Bedrooms 睡房<span> <?php echo $propertyObject['bedrooms'];?></span></li>
                    <li>Bathrooms 卫生间<span> <?php echo $propertyObject['bathrooms'];?></span> </li>
                    <li>Square Footage 套内面积<span><?php echo $propertyObject['floor_area_total'];?></span></li>
                    <li>Type 房型<span><?php echo $propertyObject['type_of_dwelling'];?></span></li>
                  </ul>
                </div>
                <div class="span260px pull-right">
                  <ul class="title-info">
                  	<li>Listing ID:<span> <?php echo $propertyObject['mls_number'];?></span></li>
                    <li>Year 建造年份<span> <?php echo ($propertyObject['built_year']=='0000')?'Not provided':$propertyObject['built_year'];?></span> </li>
                    <li>City 城市<span> <?php echo $propertyObject['city'];?></span> </li>
                    <li>Land Size 土地面积<span><?php echo $propertyObject['lot_size_sqt'];?></span></li>
                    
                    
                  </ul>
                </div>
              </div>
              <div class="excerpt">
              	<p><h3>General Description 楼盘简介</h3></p>
                <?php echo empty($propertyObject['public_remarks'])?'Not provided':$propertyObject['public_remarks'];?>
              </div>
              <div class="excerpt">
              	<p><h3>Site Influences 楼盘特色</h3></p>
              	<?php echo empty($propertyObject['site_influences'])?'Not provided':$propertyObject['site_influences'];?>
              </div>
              <div class="excerpt">
              <hr>
              <p><h3>Disclaimer 版权声明</h3></p>
              This representation is based in whole or in part on data generated by the Chilliwack & District Real Estate Board, Fraser Valley Real Estate Board or Real Estate Board of Greater Vancouver which assumes no responsibility for its accuracy. <br/>Chinese content of MLS listing information are the translation from English MLS Reciprocity. Real Estate Board of Greater Vancouver or the Fraser Valley Real Estate Board reserve the copyright of all English and non-English MLS content.
              </div>
              <div class="mls-tm"><img src="img/mlsrlogo.gif"/></div>
              <div class="share">
                <ul>
                  <li><a href="#"><img alt=""  src="img/icon/pinshare.jpg"></a></li>
                  <li><a href="#"><img alt=""  src="img/icon/twittershare.jpg"></a></li>
                  <li><a href="#"><img alt=""  src="img/icon/faceshare.jpg"></a></li>
                </ul>
              </div>
            </div>
          </div>
          <!-- End Property -->
        </div>
        <!-- End Main content -->  
        
        
        <!-- Sidebar left  -->
        <div class="span4">
          <div class="box-siderbar-container">
            <!-- sidebar-box map-box -->
            <div class="sidebar-box map-box">
              <h3>Map & Directions 楼盘地图</h3>
              
              <div id="map_canvas" style="width:260px; height:285px;"></div>
            </div>
            <!-- End sidebar-box map-box -->
            <!-- sidebar-box our-box -->
            
            
			<?php
			$listing_brokerage='';
			for($i=0;$i<2;$i++){
				if($realtor_profile_array[$i]&&$firm_profile_array[$i]){
					$listing_brokerage.='<div class="sidebar-box our-box">';
						$listing_brokerage.='<h3>'.$realtor_profile_array[$i]['name'].'</h3>';
						$listing_brokerage.='<hr>';
						//$listing_brokerage.='<ul>';
							//$listing_brokerage.='<li>';
								//$listing_brokerage.='<span>Phone: '.($realtor_profile_array[$i]['phone']?$realtor_profile_array[$i]['phone']:'N/A').'</span><br/>';
								//$listing_brokerage.='<span>Website: <a href="http://'.$realtor_profile_array[$i]['url'].'">'.$realtor_profile_array[$i]['url'].'</a></span><br/>';
							//$listing_brokerage.='</li>';
							//$listing_brokerage.='<li>';
								$listing_brokerage.='<b>'.$firm_profile_array[$i]['name'].'</b>';
								//$listing_brokerage.='<span>Phone: '.($firm_profile_array[$i]['phone']?$firm_profile_array[$i]['phone']:'N/A').'</span><br/>';
								//$listing_brokerage.='<span>Fax: '.($firm_profile_array[$i]['fax']?$firm_profile_array[$i]['fax']:'N/A').'</span><br/>';
								//$listing_brokerage.='<span>Website: <a href="http://'.$firm_profile_array[$i]['url'].'">'.$firm_profile_array[$i]['url'].'</a></span><br/>';
							//$listing_brokerage.='</li>';
						//$listing_brokerage.='</ul>';
					$listing_brokerage.='</div>';
				}
			}
			echo $listing_brokerage;
			
			?>
             
            
            <!-- sidebar-box our-box -->
            <!--
            <div class="sidebar-box our-box">
              <h3>our agents 找经纪人帮忙</h3>
              <ul>
                <li>
                  <div class="our-border clearfix">
                    <div class="our-img"><img alt="" height="90" width="90" src="img/imgdemo/90x90.gif"></div>
                    <div class="our-info">
                      <h5>Allison Jiang</h5>
                      <span></span>604.356.0707<br/>
                      <span></span><a href="mailto:allison.jiang@yahoo.com?Subject=Hello%20again">allison.jiang@yahoo.com</a>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            -->
            <!-- End sidebar-box our-box -->
            
            
            <!-- sidebar-box product_list_wg -->
            <div class="sidebar-box">
              <h3>Related Properties 您或许感兴趣</h3>
              <ul class="product_list_wg">
              <?php 
			  	$related_html='';
			  	$selectSQL = "SELECT listings.sysid, city, address, photo_path, list_price, firm_profile.* FROM listings".
							" LEFT JOIN listing_firms ON listings.sysid=listing_firms.sysid".
							" LEFT JOIN firm_profile ON listing_firms.list_firm_1_code=firm_profile.firm_code".
							" LEFT JOIN listing_album ON listings.sysid=listing_album.sysid".
							" LEFT JOIN album_profile ON listing_album.album_id=album_profile.album_id".
							" LEFT JOIN photo_profile ON album_profile.photo_id=photo_profile.photo_id".
							" WHERE publish_on_internet='Y' AND status='A' AND cover ='Y' AND city='".$propertyObject['city']."'".
							" AND listings.sysid!=".$sysid.
							" ORDER BY list_date DESC".
							" LIMIT 0, 3";
				$get_related_property = mysql_query_or_die($selectSQL,$useradmin);
				while ($row_get_related_property=mysql_fetch_assoc($get_related_property)){
					$img_url=$row_get_related_property['photo_path'];
					$image_size=getimagesize($img_url);
					if(empty($image_size)||empty($image_size[0])||empty($image_size[1])){
						$img_url='img/imgdemo/90x54.gif';
					}
					$related_html.='<li>';
					  $related_html.='<div class="clearfix">';
						$related_html.='<a title="" href="property_detail.php?sysid='.$row_get_related_property['sysid'].'">';
						  $related_html.='<img width="90" height="54" alt="" class="thumbnail_pic" src="'.$img_url.'">';
						  $related_html.=$row_get_related_property['address'];
						$related_html.='</a>'; 
						$related_html.='<div class="amount">'.number_format($row_get_related_property['list_price']).'</div>';
						$related_html.='<span>'.$row_get_related_property['city'].'</span>';
					  $related_html.='</div>';
					  $related_html.='<div><a href="http://'.$row_get_related_property['url'].'">'.$row_get_related_property['name'].'</a></div>';
					$related_html.='</li>';
				}
				echo $related_html;
			  ?>
              </ul>
            </div>
            <!-- End sidebar-box product_list_wg -->
            
            <!-- sidebar-box searchbox -->
            <div class="sidebar-box searchbox">
              <div class="row-fluid">
                <div class="span12">
                  <div class="find">
                    <div class="tabber">
                      <div class="tabbertab">
                        <h2>for sale</h2>
                        <form>
                          <div class="span12">
                            <input type="text" class="keywordfind" placeholder="Keyword for find Property…">
                          </div>
                          <div class="span12">
                            <div class="row-fluid">
                              <div class="span6">
                                <select>
                                  <option>Min Price</option>
                                  <option>$25.000</option>
                                  <option>$50.000</option>
                                  <option>$75.000</option>
                                  <option>$100.000</option>
                                  <option>$150.000</option>
                                  <option>$200.000</option>
                                  <option>$300.000</option>
                                  <option>$400.000</option>
                                  <option>$500.000</option>
                                  <option>$750.000</option>
                                </select>
                              </div>
                              <div class="span6">
                                <select>
                                  <option>Max Price</option>
                                  <option>$50.000</option>
                                  <option>$75.000</option>
                                  <option>$100.000</option>
                                  <option>$150.000</option>
                                  <option>$200.000</option>
                                  <option>$300.000</option>
                                  <option>$400.000</option>
                                  <option>$500.000</option>
                                  <option>$750.000</option>
                                  <option>$1000.000</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="span12">
                            <div class="row-fluid">
                              <div class="span6">
                                <select>
                                  <option>Bedrooms</option>
                                  <option>1</option>
                                  <option>2</option>
                                  <option>3</option>
                                  <option>4+</option>
                                </select>
                              </div>
                              <div class="span6">
                                <select>
                                  <option>Bathrooms</option>
                                  <option>1</option>
                                  <option>2</option>
                                  <option>3</option>
                                  <option>4+</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="span12">
                            <label>
                              <select>
                                <option>Property Type</option>
                                <option>Apartment</option>
                                <option>Condo</option>
                                <option>Multi Family</option>
                                <option>Other</option>
                                <option>Single Family</option>
                                <option>Villa</option>
                              </select>
                            </label>
                            <label>	
                              <button class="search" type="button">Search</button>
                            </label>	
                          </div>
                        </form>
                      </div>
                      <div class="tabbertab">
                        <h2>for rent</h2>
                        <form>
                          <div class="span12">
                            <input type="text" class="keywordfind" placeholder="Keyword for find Property…">
                          </div>
                          <div class="span12">
                            <div class="row-fluid">
                              <div class="span6">
                                <select>
                                  <option>Min Price</option>
                                  <option>$ 1000</option>
                                  <option>$ 2000</option>
                                  <option>$ 3000</option>
                                  <option>Other</option>
                                </select>
                              </div>
                              <div class="span6">
                                <select>
                                  <option>Max Price</option>
                                  <option>$ 100000</option>
                                  <option>$ 300000</option>
                                  <option>$ 3000000</option>
                                  <option>Other</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="span12">
                            <div class="row-fluid">
                              <div class="span6">
                                <select>
                                  <option>Bedrooms</option>
                                  <option>Bedrooms</option>
                                  <option>Bedrooms</option>
                                  <option>Bedrooms</option>
                                  <option>Bedrooms</option>
                                </select>
                              </div>
                              <div class="span6">
                                <select>
                                  <option>Bathrooms</option>
                                  <option>2</option>
                                  <option>3</option>
                                  <option>4</option>
                                  <option>5</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="span12">
                            <label>
                              <select>
                                <option>Property Type</option>
                                <option>Apartment</option>
                                <option>Condo</option>
                                <option>Multi Family</option>
                                <option>Other</option>
                                <option>Single Family</option>
                                <option>Villa</option>
                              </select>
                            </label>
                            <label>	
                              <button class="search" type="button">Search</button>
                            </label>	
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End sidebar-box searchbox -->
            
          </div>
        </div>
        <!-- End Sidebar left  -->
        
      </div>
    </div>
  </div>
</div>
</div>
<!-- END CONTENT -->

<!-- BEGIN FOOTER -->
<footer>
  <?php require_once'html/footer.php'; ?>
</footer>
<!-- END FOOTER -->
<div id='bttop'>BACK TO TOP</div>

<!-- Always latest version of jQuery-->
<script src="../test1/js/jquery-1.8.3.min.js"></script>
<script src="../test1/js/bootstrap.min.js"></script>
<!-- Some scripts that are used in almost every page -->
<script src="../test1/js/tinynav/tinynav.js" type="text/javascript"></script>
<script type="text/javascript" src="../test1/js/tabber/tabber.js"></script>
<!-- Load template main javascript file -->
<script type="text/javascript" src="../test1/js/main.js"></script>

<!-- ===================================================== -->
<!-- ================ Property-detail page only scripts ============ -->
<script src="../test1/js/flexflider/jquery.flexslider-min.js"></script>
<script src="../test1/js/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo 'AIzaSyAQfgZWt00K1p5pfsDmrylZ9fHquHNcDMw';?>&sensor=false"></script>
<script type="text/javascript">
var photoArray=<?php echo json_encode($photo_array)?>;
/* <![CDATA[ */
jQuery(function($){
  
});
$(document).ready(function() {
	initialize_map(<?php echo isset($geo_address)?$geo_address['lat']:49.261226;?>, <?php echo isset($geo_address)?$geo_address['lng']:-123.1139268;?>);
	$('#pic-control').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 55,
    itemMargin: 10,
    maxItems: 7,
    asNavFor: '#pic-detail'
  	});

  $('#pic-detail').flexslider({
    controlNav: false,
    directionNav: false,
    animationLoop: false,
    slideshow: false,
    sync: "#pic-control",
    start: function(slider){
      $('body').removeClass('loading');
    }
  	});

  $(".detailbox").colorbox({rel:'detailbox'});
  if($('ul#pic-nav li').length<1){
	  //alert($('ul#pic-nav li').length);
	  $('#pic-control').hide();
  }
});
/* ]]> */
function initialize_map(lat, lng){
	var myLatlng= new google.maps.LatLng(lat, lng)
	var mapOptions={
		center: myLatlng,
		zoom:12
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);
	var marker = new google.maps.Marker({
		draggable: true,
		position: myLatlng, 
		map: map,
		title: "Your location"
	});
	
}
</script>
</body>
</html>

