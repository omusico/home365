

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Listings | 楼盘列表 | Allison Jiang's Real Estate Site | 地产知道 | 大温地产专业网站</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <!-- Your styles -->
  <link href="../test1/css/bootstrap.css" rel="stylesheet" media="screen">
  <link href="../test1/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
  <link href="../test1/css/flexslider/flexslider.css" rel="stylesheet" media="screen">
  <link href="../test1/css/tabber/tabber.css" rel="stylesheet" media="screen">
  <link href="css/styles.css" rel="stylesheet" media="screen">
  <link href="../test1/css/responsive.css" rel="stylesheet" media="screen">
  <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Raleway:400,700,100,200,300' rel='stylesheet' type='text/css'>
  <!-- HTML5 shim, for Ie6-8 support of HTML5 elements -->
    <!--[if lt Ie 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <?php 
	//specify how many properties you want to put into listings div
	require_once'../test1/server/get_properties_body.php';
	$from=0;
	$size=6;
	$theme='list';
	$properties_array=get_properties_summary($from,$size,$theme);
	?>
    <script type="text/javascript">
	var propertiesArray=<?php echo json_encode($properties_array);?>;
	var pages=Math.ceil(propertiesArray['rows']/<?php echo $size;?>);
    </script>
  </head>
  <body>
    <!-- BEGIN HEADER -->
    <header>
    <?php require_once'../test1/html/header.php'; ?>
   
   <!-- Banner -->
   <div class="row-fluid">
    <div class="span12">
      <section class="pic-cat">
        <img width="1900" height="200" alt="" src="img/imgdemo/1900x200.gif">
      </section>
    </div>
  </div>
  <!-- End Banner -->
  
</header>
<!-- END HEADER -->

<!-- BEGIN CONTENT -->
<div class="main-content">
	<!-- Tabber Find -->
	<!-- Tabber -->
  <div class="find">
    <div class="container">
      <div class="tabber">
        <div class="tabbertab">
          <h2>for sale</h2>
          <form>
            <div class="row">
              <div class="span6">
                <label>
                  <input type="text" id="search_keyword" class="keywordfind" placeholder="City/Postal Code/Street Name...">
                </label>
                <div class="row">
                  <div class="span3">
                    <select id="search_min_price">
                      <option value="">Min Price</option>
                      <option value="25000">$25.000</option>
                      <option value="50000">$50.000</option>
                      <option value="75000">$75.000</option>
                      <option value="100000">$100.000</option>
                      <option value="150000">$150.000</option>
                      <option value="200000">$200.000</option>
                      <option value="300000">$300.000</option>
                      <option value="400000">$400.000</option>
                      <option value="500000">$500.000</option>
                      <option value="750000">$750.000</option>
                    </select>
                  </div>
                  <div class="span3">
                    <select id="search_max_price">
                      <option value="">Max Price</option>
                      <option value="50000">$50.000</option>
                      <option value="75000">$75.000</option>
                      <option value="100000">$100.000</option>
                      <option value="150000">$150.000</option>
                      <option value="200000">$200.000</option>
                      <option value="300000">$300.000</option>
                      <option value="400000">$400.000</option>
                      <option value="500000">$500.000</option>
                      <option value="750000">$750.000</option>
                      <option value="1000000">$1000.000</option>
                      <option value="2000000">$2000.000</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="span3">
                <label>
                  <select id="search_bedrooms">
                    <option value="">Bedrooms</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4+</option>
                  </select>
                </label>
                <label>
                  <select id="search_property_type">
                    <option value="">Property Type</option>
                    <option value="apartment">Apartment</option>
                    <option value="condo">Condo</option>
                    <option value="multifamily">Multi Family</option>
                    <option value="other">Other</option>
                    <option value="singlefamily">Single Family</option>
                    <option value="villa">Villa</option>
                  </select>
                </label>
              </div>
              <div class="span3">
                <label>
                  <select id="search_bathrooms">
                    <option value="">Bathrooms</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3+</option>
                  </select>
                </label>
                <label>	
                  <button class="search" type="button" onClick="search_properties()">Search</button>
                </label>	
              </div>
            </div>
          </form>
        </div>
        
      </div>
    </div>
  </div>
  <!-- Tabber -->
  <!-- End Tabber Find -->
  
  <div class="properties">
    <div class="container">
      <!-- Full width 1 -->
      <div class="grid_full_width" id="fullwidth1">
        <div class="all-text">
          <h3>All Properties 全部楼盘</h3>
          
        </div>
        <div class="shop-nav clearfix">
          <div class="row">
            <div class="span6">
              <div class="list-grid inleft">
                <ul>
                  <!--<li><a href="grid_fullwidth_4_column.html"><i class="grid4col"></i></a></li>-->
                  
                  <li><a class="active" href="list_fullwidth1.php"><i class="grid2list"></i></a></li>
                </ul>
              </div>
            </div>
            <div class="span6">
              <div class="ordering pull-right">
                <select class="orderby">
                  <option>Price, Low to High</option>
                  <option>Sort by most recent</option>
                  <option>Sort by price</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="grid_list_product">
            <ul class="products" id="able-list">
              <!--This is where you put the property list.-->
            </ul>
          </div>
        </div>
        <!-- Page-ination -->
        <div class="page-ination">
          <div class="page-in">
            <div class="clearfix" id="properties_nav">
              
            </div>
          </div>
        </div>
        <!-- End Page-ination -->
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
<script type="text/javascript">
$(document).ready(function(){
	populate_properties(propertiesArray);
	add_navigation('properties_nav',pages);           
});
function search_properties(){
	var keyword = $('#search_keyword').val();
	var bedrooms = parseInt($('#search_bedrooms').val());
	var bathrooms = parseInt($('#search_bathrooms').val());
	var min_price = parseInt($('#search_min_price').val());
	var max_price = parseInt($('#search_max_price').val());
	var property_type = $('#search_property_type').val();
	
	if(max_price>min_price){
		var post_data = new Object;
		post_data['keyword']=keyword;
		post_data['bedrooms']=bedrooms;
		post_data['bathrooms']=bathrooms;
		post_data['min_price']=min_price;
		post_data['max_price']=max_price;
		post_data['property_type']=property_type;
		$.post('server/search_properties.php',post_data,function(){
			var return_data = arguments[0];
			populate_properties(return_data);
		},'json');
	}else{
		alert('Max Price should be greater than Min Price!');
	}
}
function populate_properties(propertiesArray){
	$('#able-list').empty();
	for(var i=0;i<propertiesArray['html'].length;i++){
		$('#able-list').append(propertiesArray['html'][i]);
	}
}
function add_navigation(nav_id,pages){
	$('#'+nav_id).append('<a href="#javascript:void(0)"onClick="get_properties(0);$(this).addClass(\'selected\').siblings().removeClass(\'selected\');"><img alt="" src="img/icon/pre2.png"></a>');
	$('#'+nav_id).append('<a href="#"><img alt="" src="img/icon/pre1.png"></a>');
	if(pages>=5){
		for(var i=1; i<=5; i++){
			$('#'+nav_id).append('<a id="nav_'+i+'" href="javascript:void(0)"onclick="get_properties('+(i-1)*<?php echo $size;?>+');$(this).addClass(\'selected\').siblings().removeClass(\'selected\');">'+i+'</a>');
		}
	}
	$('#'+nav_id).append('<a href="javascript:void(0)"onClick="next_navigation('+nav_id+','+pages+')"><img alt="" src="img/icon/next1.png"></a>');
	$('#'+nav_id).append('<a href="javascript:void(0)"onClick="get_properties('+pages+');$(this).addClass(\'selected\').siblings().removeClass(\'selected\');"><img alt="" src="img/icon/next2.png"></a>');	
}
function next_navigation(nav_id,pages){
	var selected_id=$('#properties_nav > a.selected').attr('id');
	if(selected_id!=null){
		var strArray = selected_id.match(/(\d+)/g);
		var selected_index  = parseInt(strArray[0]);
	}else{
		var selected_index = 0;
	}
	var new_index=selected_index+1;
	get_properties(new_index-1);
	/*if(new_index>5){
		for(var i=new_index; i<=new_index+4; i++){
			$('#'+nav_id).append('<a id="nav_'+i+'" href="javascript:void(0)"onclick="get_properties('+(i-1)*<?php echo $size;?>+');$(this).addClass(\'selected\').siblings().removeClass(\'selected\');">'+i+'</a>');
		}
	}*/
	$('#nav_'+new_index).addClass('selected').siblings().removeClass('selected');
	
}
function get_properties(from){
	size=<?php echo $size;?>;
	var post_data = new Object;
	post_data['from']=from;
	post_data['size']=size;
	post_data['theme']='list';
	//alert(post_data.toSource());
	$.post('server/get_properties.php',post_data,function() {
		var return_data = arguments[0];
		populate_properties(return_data);
	},"json");
	
}
</script>
</body>
</html>

