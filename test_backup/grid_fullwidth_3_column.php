<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>RealEstast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <!-- Your styles -->
  <link href="../test1/css/bootstrap.css" rel="stylesheet" media="screen">
  <link href="../test1/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
  <link href="../test1/css/flexslider/flexslider.css" rel="stylesheet" media="screen">
  <link href="../test1/css/tabber/tabber.css" rel="stylesheet" media="screen">
  <link href="../site/css/styles.css" rel="stylesheet" media="screen">
  <link href="../test1/css/responsive.css" rel="stylesheet" media="screen">
  <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Raleway:400,700,100,200,300' rel='stylesheet' type='text/css'>
  <!-- HTML5 shim, for Ie6-8 support of HTML5 elements -->
    <!--[if lt Ie 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <!-- BEGIN HEADER  -->
    <header>
    <?php require_once '../test1/html/header.php';?>
   
   <!-- Banner -->
   <div class="row-fluid">
    <div class="span12">
      <section class="pic-cat">
        <img width="1900" height="200" alt="" src="../site/img/imgdemo/1900x200.gif">
      </section>
    </div>
  </div>
  <!-- End Banner -->
  
</header>
<!-- END HEADER  -->

<!-- BEGIN CONTENT  -->
<div class="main-content">
	
	<!-- Find Tabber Find-->
  <div class="find">
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
                  <button class="search" type="button">Search</button>
                </label>	
              </div>
            </div>
          </form>
        </div>
        <div class="tabbertab">
          <h2>for rent</h2>
          <form>
            <div class="row">
              <div class="span6">
                <label>
                  <input type="text" class="keywordfind" placeholder="Keyword for find Property… 2">
                </label>
                <div class="row">
                  <div class="span3">
                    <select>
                      <option>Min Price</option>
                      <option>Min Price</option>
                      <option>Min Price</option>
                      <option>Min Price</option>
                      <option>Min Price</option>
                    </select>
                  </div>
                  <div class="span3">
                    <select>
                      <option>Max Price</option>
                      <option>Max Price</option>
                      <option>Max Price</option>
                      <option>Max Price</option>
                      <option>Max Price</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="span3">
                <label>
                  <select>
                    <option>Bedrooms</option>
                    <option>Bedrooms</option>
                    <option>Bedrooms</option>
                    <option>Bedrooms</option>
                    <option>Bedrooms</option>
                  </select>
                </label>
                <label>
                  <select>
                    <option>Property Type</option>
                    <option>Property Type</option>
                    <option>Property Type</option>
                    <option>Property Type</option>
                    <option>Property Type</option>
                  </select>
                </label>
              </div>
              <div class="span3">
                <label>
                  <select>
                    <option>Bathrooms</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                  </select>
                </label>
                <label>	
                  <button class="search" type="button">Search</button>
                </label>	
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End Tabber Find -->
  
  
  <div class="properties">
    <div class="container">
      <!-- Grid full width 3 column -->
      <div class="grid_full_width" id="3column">
        <div class="all-text">
          <h3>All Properties </h3>
          <p>
            Nam nec tellus a odio tincidunt auctor a ornare odio. Sed non  mauris vitae erat consequat auctor eu in elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris in erat justo. Nullam ac urna eu felis dapibus condimentum sit amet a augue. Sed non neque elit. Sed ut imperdiet nisi. Proin condimentum fermentum nunc. Etiam pharetra, erat sed fermentum feugiat, velit mauris egestas quam, ut aliquam massa nisl quis neque. Suspendisse in orci enim.
          </p>
        </div>
        <div class="shop-nav clearfix">
          <div class="row">
            <div class="span6">
              <div class="list-grid inleft">
                <ul>
                  <li><a href="../test1/grid_fullwidth_4_column.html"><i class="grid4col"></i></a></li>
                  <li><a class="active" href="../site/grid_fullwidth_3_column.php"><i class="grid3col"></i></a></li>
                  <li><a href="../test1/list_fullwidth2.html"><i class="grid2list"></i></a></li>
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
          <ul class="products" id="able-list">
            <li style="display: block;" class="span4 first house offices Residential">
              <div class="product-item">
                <div class="imagewrapper">
                  <img alt="" width="300" height="180" src="../site/img/imgdemo/300x180.gif">
                  <span class="price"> $358.000</span>
                </div>
                <h3><a href="../site/property_detail.php" title=""> 1076 Nelson Walk </a></h3>
                <ul class="title-info">
                  <li>Bathrooms <span> 1</span> </li>
                  <li>Bathrooms <span> 1</span></li>
                  <li>Square Footage <span>3200 sqft</span></li>
                  <li>Type: <span>Residential</span></li>
                </ul>
              </div>
            </li>
            <li class="span4 house offices apartment Residential">
              <div class="product-item">
                <div class="imagewrapper">
                  <img alt="" width="300" height="180" src="../site/img/imgdemo/300x180.gif">
                  <span class="price"> $358.000</span>
                </div>
                <h3><a href="../site/property_detail.php" title=""> 4101 Gulf Shore </a></h3>
                <ul class="title-info">
                  <li>Bathrooms <span> 1</span> </li>
                  <li>Bathrooms <span> 1</span></li>
                  <li>Square Footage <span>3200 sqft</span></li>
                  <li>Type: <span>Residential</span></li>
                </ul>
              </div>
            </li>
            <li class="span4 product last house offices apartment">
              <div class="product-item">
                <div class="imagewrapper">
                  <img alt="" width="300" height="180" src="../site/img/imgdemo/300x180.gif">
                  <span class="price"> $358.000</span>
                </div>
                <h3><a href="../site/property_detail.php" title=""> 3900 Rum Row</a></h3>
                <ul class="title-info">
                  <li>Bathrooms <span> 1</span> </li>
                  <li>Bathrooms <span> 1</span></li>
                  <li>Square Footage <span>3200 sqft</span></li>
                  <li>Type: <span>Residential</span></li>
                </ul>
              </div>
            </li>
            <li class="span4 product first offices apartment Residential">
              <div class="product-item">
                <div class="imagewrapper">
                  <img alt="" width="300" height="180" src="../site/img/imgdemo/300x180.gif">
                  <span class="price"> $358.000</span>
                </div>
                <h3><a href="../site/property_detail.php" title=""> 1085 Nelsons Walk </a></h3>
                <ul class="title-info">
                  <li>Bathrooms <span> 1</span> </li>
                  <li>Bathrooms <span> 1</span></li>
                  <li>Square Footage <span>3200 sqft</span></li>
                  <li>Type: <span>Residential</span></li>
                </ul>
              </div>
            </li>
            <li class="span4 house offices apartment Residential">
              <div class="product-item">
                <div class="imagewrapper">
                  <img alt="" width="300" height="180" src="../site/img/imgdemo/300x180.gif">
                  <span class="price"> $358.000</span>
                </div>
                <h3><a href="../site/property_detail.php" title=""> 3675 Gordon Dr </a></h3>
                <ul class="title-info">
                  <li>Bathrooms <span> 1</span> </li>
                  <li>Bathrooms <span> 1</span></li>
                  <li>Square Footage <span>3200 sqft</span></li>
                  <li>Type: <span>Residential</span></li>
                </ul>
              </div>
            </li>
            <li class="span4 product last house offices Residential">
              <div class="product-item">
                <div class="imagewrapper">
                  <img alt="" width="300" height="180" src="../site/img/imgdemo/300x180.gif">
                  <span class="price"> $358.000</span>
                </div>
                <h3><a href="../site/property_detail.php" title=""> 204 temporibus vis  </a></h3>
                <ul class="title-info">
                  <li>Bathrooms <span> 1</span> </li>
                  <li>Bathrooms <span> 1</span></li>
                  <li>Square Footage <span>3200 sqft</span></li>
                  <li>Type: <span>Residential</span></li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
        <!-- Page-ination -->
        <div class="page-ination">
          <div class="page-in">
            <ul class="clearfix">
              <li><a href="#"><img alt="" src="../site/img/icon/pre2.png"></a></li>
              <li><a href="#"><img alt="" src="../site/img/icon/pre1.png"></a></li>
              <li><a class="current" href="#">1</a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">4</a></li>
              <li><a href="#">5</a></li>
              <li><a href="#"><img alt="" src="../site/img/icon/next1.png"></a></li>
              <li><a href="#"><img alt="" src="../site/img/icon/next2.png"></a></li>
            </ul>
          </div>
        </div>
        <!-- End Page-ination -->
        
      </div>
    </div>
  </div>
</div>
<!-- END CONTENT  -->

<!-- BEGIN FOOTER -->
<footer>
  <div class="footer-container">
    <div class="container">
      <!-- Footer box -->
      <div class="footer-top">
        <div class="row">
          <div class="span4">
            <h3>contact detail</h3>
            <p>Pellentesque nec erat. Aenean semper, neque non faucibus. Malesuada, dui felis tempor felis, vel varius ante diam ut mauris. </p>
            <p><span>Phone. 012.666.999 </span><br/><span>Fax. 012.666.999 </span><br/><span>Mail. <a href="mailto:someone@example.com?Subject=Hello%20again">Pixelgeeklab@gmail.com</a>  </span><br/></p>
          </div>
          <div class="span4">
            <h3>Useful links</h3>
            <ul>
              <li><a href="#" title="">Help and FAQs</a></li>
              <li><a href="#" title="">Home Price</a></li>
              <li><a href="#" title="">Market View</a></li>
              <li><a href="#" title="">Free Credit Report</a></li>
              <li><a href="#" title="">Terms and Conditions</a></li>
              <li><a href="#" title="">Privacy Policy</a></li>
              <li><a href="#" title="">Community Guidelines</a></li>
            </ul>
          </div>
          <div class="span4">
            <h3>don’t miss out</h3>
            <p>In venenatis neque a eros laoreet eu placerat erat suscipit. Fusce cursus, erat ut scelerisque condimentum, quam odio ultrices leo.</p>
            <div class="newletter">
              <form>
                <input type="text" class="textnewletter" placeholder="Enter your email here…">
                <button type="submit" class="buttonnewletter">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End footer box -->
      <div class="footer-bottom">
        <div class="row">
          <div class="span6">
            <p>Copyright © 2013 PGL RealEstast. Designed by <a href="#" title="">PixelGeekLab</a><br/>All rights reserved.</p>
          </div>
          <div class="span6">
            <div class="social pull-right">
              <ul>
                <li><a class="facebook" title="" href="#"> Facebook </a></li>
                <li><a class="twitter" title="" href="#"> twitter </a></li>
                <li><a class="googplus" title="" href="#"> googplus </a></li>
                <li><a class="pinterest" title="" href="#"> pinterest </a></li>
                <li><a class="email" title="" href="#"> Email </a></li>
                <li><a class="feed" title="" href="#"> Feed </a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
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
</body>
</html>

