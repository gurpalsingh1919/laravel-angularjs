<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Landing page</title>

    <!-- Bootstrap -->
    <link href="public/assets/frontend/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom -->
    <link href="public/assets/frontend/css/custom_frontend.css" rel="stylesheet">
    
    <!-- fonts icon -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    
    <!-- fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,500,300,700,900' rel='stylesheet' type='text/css'>
    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
  </head>
  <body>
  
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="javascript:void(0)" class="navbar-brand"><img src="public/assets/admin/images/launchafunnel_logo.png" alt="logo" /></a>
        </div>
        <div class="navbar-collapse collapse navbar-right" id="navbar">
          <ul class="nav navbar-nav">
           <!--  <li ><a href="javascript:void(0)"  target="_self">Benefits</a></li>
            <li><a href="javascript:void(0)"  target="_self">Tutorial</a></li>
            <li><a href="javascript:void(0)"  target="_self">Clients</a></li> 
            <li><a href="javascript:void(0)"  target="_self">Prices</a></li> -->
            <li><a ui-sref="usersignup" ng-click="signup()" href="javascript:void(0)"  target="_self" class="btn">Get Started</a></li>
            <li><a href="#/login" target="_self" class="btn" >Login</a></li>
          </ul>
         <!-- <ul class="nav navbar-nav">
            <li><a href="#login" class="btn login-btn">Login</a></li>
          </ul>-->
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron banner-home">
      	<div class="container">
        	<h5>The Best Start</h5>
            <h1>For Your Business</h1>
            <p class="para">No fluff. Nothing should lead the visitor away from the main essence of website. There must be just important information.</p>
            <p>
              <a role="button" href="sign-up.html" class="btn view-btn">View more</a>
              <a role="button" href="sign-up.html" class="btn create-btn">Create your Theme</a>
            </p>
            <img src="images/show-case.png" alt="Show Case" />
        </div>
      </div>
      
      <div class="feature">
          <div class="container">
          <!-- Three columns of text below the carousel -->
          <div class="row">
            <div class="col-lg-4">
              <img src="images/feature-responsive.png" />
              <h2>Fully responsive</h2>
              <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac.</p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-4">
              <img src="images/feature-pure.png" />
              <h2>Pure & Simple</h2>
              <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus.</p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-4">
              <img src="images/feature-color.png" />
              <h2>Color Schemes</h2>
              <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo.</p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-4">
              <img src="images/feature-psd.png" />
              <h2>PSD Is Included</h2>
              <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo.</p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-4">
              <img src="images/feature-document.png" />
              <h2>Documentation</h2>
              <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo.</p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-4">
              <img src="images/feature-multiplatform.png" />
              <h2>Multiplatform</h2>
              <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo.</p>
            </div><!-- /.col-lg-4 -->
          </div><!-- /.row -->
        </div>
    </div>
     
     <div class="tool">
     	<div class="container">
        	<h3>Great tool for All</h3>
            <i>Modern solution</i>
            <p>In our work we try to use only the most modern, convenient and interesting<br>solutions. We want the template you downloaded look unique and new for<br>such a long time as it is possible. </p>
            <img src="images/tool-banner.png" alt="Tools" />
        </div>
     </div>
     
     <div class="video">
     	<div class="container">
        	<h3>A page builder for the next generation.</h3>
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,<br>totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi. </p>
            <img src="images/video.png" alt="Video" />
        </div>
     </div>
     
     <div class="pricing">
     	<div class="container">
        	<h3 class="text-center">Pricing? That's easy.</h3>
            <div class="pricing-row">
            	<div class="pricing-bx">
                	<p class="user-type">One User</p>
                    <p class="price">$12</p>
                    <p class="duration">Per Month</p>
                    <a href="#" class="btn">Get Started</a>
                    <p class="requirement">30 Day Free Trial<br>No Credit Card Required </p>
                </div>
                <div class="pricing-bx blue-bx">
                	<p class="user-type">Unlimited Users</p>
<p class="price">$29</p>
                    <p class="duration">Per Month</p>
                    <a href="#" class="btn">Get Started</a>
                    <p class="requirement">30 Day Free Trial<br>No Credit Card Required </p>
                </div>
            </div>
        </div>
     </div>
  
  <div class="newsletter">
     	<div class="container">
        	<h3>Start building more beautiful pages today.</h3>
			<div class="row">
       			<div class="col-md-6">
	            	<input type="text" placeholder="Email Address" />
                </div>
                <div class="col-md-6">
                	<button>Start Free</button>
                </div>
            </div>
            <p>Free 30 day trial - No credit card required.</p>
        </div>
     </div>
     
     <div class="footer">
     	<div class="container">
        	<button class="top-btn">Top</button>
        	<div class="row">
            	<div class="col-xs-12 col-md-3">
                	<img src="images/logo.png" alt="logo" />
                </div>
                <div class="col-xs-12 col-md-3">
                	<h4>Recent Posts</h4>
                    <div class="post">
                        <p><a href="#"><strong>Hugging pugs is super trendy</strong></a></p>
                        <p>February 14, 2015</p>
                    </div>
                    <div class="post">
                        <p><a href="#"><strong>Hugging pugs is super trendy</strong></a></p>
                        <p>February 14, 2015</p>
                    </div>
                    <div class="post">
                        <p><a href="#"><strong>Hugging pugs is super trendy</strong></a></p>
                        <p>February 14, 2015</p>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3">
                	<h4>latest updates</h4>
                    <ul class="tweets">
                    	<li>
                            <i class="fa fa-twitter"></i> 
                            <span>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium..</span>
                        </li>
                        <li> 
                            <i class="fa fa-twitter"></i> 
                            <span>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae</span>
                        </li>
                    </ul>
                </div>
                <div class="col-xs-12 col-md-3">
                	<h4>Instagram</h4>
                    <ul class="gallery">
                    	<li>
                        	<a href="#"><img src="images/gallery-pic.png" alt="instagram" /></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row bottom">
            	<div class="col-xs-12 col-md-6">
                	<span class="copywrite">© 2015 - Page Builder.</span>
                </div>
                <div class="col-xs-12 col-md-6">
                	<ul class="social">
                    	<li>
                        	<a href="#"><i class="fa fa-twitter"></i></a>	
                        </li>
                        <li>
                        	<a href="#"><i class="fa fa-facebook"></i></a>	
                        </li>
                        <li>
                        	<a href="#"><i class="fa fa-dribbble"></i></a>	
                        </li>
                        <li>
                        	<a href="#"><i class="fa fa-vimeo"></i></a>	
                        </li>
                    </ul>
                </div>
            </div>
        </div>
     </div>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
  </body>
</html>
