<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Allison Jiang's Real Estate Site | 地产知道 | 大温地产专业网站</title>
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
    <!-- styles for Ie -->
    <?php 
	require_once'../test1/server/get_properties_body.php';
	//specify how many properties you want to put into listings div
	$from=0;
	$size=15;
	$theme='home';
	$properties_array=get_properties_summary($from,$size,$theme);
	?>
    <script type="text/javascript">
	var propertiesArray=<?php echo json_encode($properties_array);?>;
	//alert(propertiesArray['rows']);
    </script>
  </head>
  <body>
    <!-- BEGIN HEADER -->
    <header>
    <?php require_once'../test1/html/header.php'; ?>
   
   <!-- Slider Home -->
   
   <!-- Flexslider -->
   <div id="slider-home" class="row-fluid slider-home">
    <div class="span12">
      <section class="slider">
        <div id="main-slider" class="flexslider">
          <ul class="slides">
            <li>
              <div class="container flex-caption">
                <div class="title">
                  <span> 
                    140 SPRING LANES <br/> 
                    BEVERLY HILLS 	
                  </span>
                </div>
              </div>
              <img alt="" width="1900" height="600" src="img/imgdemo/1900x600.gif" />
            </li>
            <li>
              <div class="container flex-caption">
                <div class="title">
                  <span>
                    140 SPRING LANES <br/>
                    BEVERLY HILLS
                  </span>
                </div>
              </div>
              <img alt="" width="1440" height="600" src="img/imgdemo/1900x600-2.gif" />
            </li>
            <li>
              <div class="container flex-caption">
                <div class="title">
                  <span>
                    140 SPRING LANES <br/>
                    BEVERLY HILLS
                  </span>
                </div>
              </div>
              <img alt="" width="1440" height="600" src="img/imgdemo/1900x600.gif" />
            </li>
            <li>
              <div class="container flex-caption">
                <div class="title">
                  <span>
                    140 SPRING LANES <br/>
                    BEVERLY HILLS
                  </span>
                </div>
              </div>
              <img alt="" width="1440" height="600" src="img/imgdemo/1900x600-2.gif" />
            </li>
            <li>
              <div class="container flex-caption">
                <div class="title">
                  <span>
                    140 SPRING LANES <br/>
                    BEVERLY HILLS
                  </span>
                </div>
              </div>
              <img alt="" width="1440" height="600" src="img/imgdemo/1900x600.gif" />
            </li>
          </ul>
        </div>
      </section>
    </div>
  </div>
  <!-- End Flexslider -->
  
  <!-- End Slider home -->
  
</header>

<!-- END HEADER -->

<!-- BEGIN CONTENT -->
<div class="main-content">
	<!-- Tabber Find -->
	<!-- Tabber -->
  <div id="findtabber" class="find">
    <div class="container">
      <div class="tabber">
        <div class="tabbertab">
          <h2>for sale</h2>
          <form>
            <div class="row">
              <div class="span6">
                <label>
                  <input type="text" class="keywordfind" placeholder="Keyword for find Property…">
                </label>
                <div class="row">
                  <div class="span3">
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
                  <div class="span3">
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
              <div class="span3">
                <label>
                  <select>
                    <option>Bedrooms</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4+</option>
                  </select>
                </label>
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
              </div>
              <div class="span3">
                <label>
                  <select>
                    <option>Bathrooms</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4+</option>
                  </select>
                </label>
                <label>	
                  <button class="search" type="button" onClick="search_property()">Search</button>
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
  
  <!-- Properties -->
  <div class="properties">
   <!-- Properties scroll -->
   
   <!-- Nicescroll -->
   <div id="property-scroll" class="container">
    <div id="wrapper">
      <div class="box">
        <div class="scroll-properties clearfix">
          <div class="row col-home">
            <div class="span6">
              <div class="container-big">
                <img alt="" src="img/imgdemo/460x460.gif">
                <article class="text-big">
                  <div class="infotexthv">
                    <h3><a href="property_detail.php" title=""> 1060 Nelsons Walk</a> </h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                  </div>
                </article>
              </div>
            </div>
            <div class="span6">
              <div class="row divspace">
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="span6">
              <div class="container-big">
                <img alt="" src="img/imgdemo/460x460.gif">
                <article class="text-big">
                  <div class="infotexthv">
                    <h3><a href="property_detail.php" title=""> 1060 Nelsons Walk</a> </h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                  </div>
                </article>
              </div>
            </div>
            <div class="span6">
              <div class="row divspace">
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
                <div class="span3">
                  <div class="container-small">
                    <img alt="" src="img/imgdemo/220x220.gif">
                    <article class="text-small">
                      <div class="infotexthv">
                        <h3><a href="property_detail.php" title=""> 2170 TARPON RD </a> </h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin rutrum nisi eu ante mattis sit amet luctus nisl tempus.</p>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Nicescroll -->
  <!-- End Scroll -->
  
  <!-- Properties List -->
  <div id="property-list" class="container">
    <h3>Recent Properties </h3>
    <!-- Filterable -->  
    <div class="filter-pro clearfix">
      <div class="row">
        <div class="span8">
          <ul  id="able-filter">
            <li><a href="#all" title="">All</a></li>
            <li><a href="#House" title="" rel="House">house</a></li>
            <li><a href="#Townhouse" title="" rel="Townhouse">town house</a></li>           
            <li><a href="#Apartment" title="" rel="Apartment">apartment</a></li>
            <li><a href="#Duplex" title="" rel="Duplex">duplex</a></li>
            </ul>
        </div>
        <div class="span4">
          
        </div>
      </div>
    </div>
    <!-- Filterable -->  
    <div class="row">
      <ul class="products" id="able-list">
      <!--This is where you put listings-->
      </ul>
    </div>
  </div>
  <!-- End Properties List -->
  
</div>
<!-- End Properties -->

<!-- Our Agents -->
<div class="ouragents">
  <div class="container">
    <h3>Our Team</h3>
    <div class="our-content">
      <div class="row">
        <ul class="clearfix">
          <li class="span6">
            <div class="our-border clearfix">
              <div class="our-img"><img alt="" src="img/imgdemo/180x180.gif"></div>
              <div class="our-info">
                <h4>No.1</h4>
                <h5>Allison Jiang</h5>
                <p>Allison Jiang, licensed REALTOR®(s) in the Province of British Columbia.</p>
                <span>Call. </span>604.356.0707 <br/>
                <span>Mail. </span><a href="mailto:allison.jiang@yahoo.com?Subject=Hello%20again">allison.jiang@yahoo.com</a>
              </div>
            </div>
          </li>
          <li class="span6">
            <div class="our-border clearfix">
             <div class="our-img"><img alt="" src="img/imgdemo/180x180.gif"></div>
              <div class="our-info">
                <h4>No.2</h4>
                <h5>Allison Jiang</h5>
                <p>Allison Jiang, licensed REALTOR®(s) in the Province of British Columbia.</p>
                <span>Call. </span>604.356.0707 <br/>
                <span>Mail. </span><a href="mailto:allison.jiang@yahoo.com?Subject=Hello%20again">allison.jiang@yahoo.com</a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    
  </div>
</div>
<!-- End Our Agents -->

</div>
<!-- END CONTENT -->

<!-- FOOTER -->
<footer>
     <?php require_once'html/footer.php'; ?>
</footer>
<div id='bttop'>BACK TO TOP</div>
<!-- END FOOTER -->

<!-- Always latest version of jQuery-->
<script src="../test1/js/jquery-1.8.3.min.js"></script>
<script src="../test1/js/bootstrap.min.js"></script>
<!-- Some scripts that are used in almost every page -->
<script src="../test1/js/tinynav/tinynav.js" type="text/javascript"></script>
<script type="text/javascript" src="../test1/js/tabber/tabber.js"></script>
<!-- Load template main javascript file -->
<script type="text/javascript" src="../test1/js/main.js"></script>

<!-- ===================================================== -->
<!-- ================ Index page only scripts ============ -->
<script src="../test1/js/flexflider/jquery.flexslider-min.js"></script>
<script src="../test1/js/nicescroll/jquery.nicescroll.js"></script>
<script src="../test1/js/filterable/filterable.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function($){
  $('#main-slider').flexslider();

  jQuery('.box').niceScroll({
    autohidemode:false,
    scrollspeed: 100,
    cursorcolor: '#d84949',
    cursorwidth: '15px',
    cursorborderradius: '0px',
    cursorborder: '0',
    background: '#dddddd'
  });
});
$(document).ready(function(){
	$('#property-scroll').hide();
	for(var i=0;i<propertiesArray['html'].length;i++){
		$('#able-list').append(propertiesArray['html'][i]);
	}
});
function search_property(){
	$('#property-scroll').show();
	jQuery(function($){
		jQuery('.box').niceScroll({
		autohidemode:false,
		scrollspeed: 100,
		cursorcolor: '#d84949',
		cursorwidth: '15px',
		cursorborderradius: '0px',
		cursorborder: '0',
		background: '#dddddd'
  		});
	});
}
/* ]]> */
</script>

</body>
</html>

