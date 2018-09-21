<!DOCTYPE html>
<html lang="en">
  <head>
   <base href="/">
	<meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Launch A Funnel</title>

    <!-- Bootstrap -->
    <link href="public/assets/frontend/css/bootstrap.min.css" rel="stylesheet"> 
 <link rel="shortcut icon" href="public/assets/admin/images/favicon.ico" type="image/x-icon"/>
    <!-- Custom -->
    <!-- link href="public/assets/frontend/css/custom.css" rel="stylesheet"> -->
	 <link href="public/assets/admin/css/jquery-ui.css" rel="stylesheet">
	  <link href="public/assets/admin/css/colorpicker.css" rel="stylesheet">
	 
	 
    <link rel="stylesheet" href="public/assets/admin/css/animate.css">
    <link rel="stylesheet" href="public/assets/admin/css/bootstrap-color-picker.css">
    <link rel="stylesheet" href="public/assets/admin/css/angular-toggle-switch.css">
	
    <!-- fonts icon -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    
    <!-- fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,500,300,700,900' rel='stylesheet' type='text/css'>
    <!-- Angular -->
	<!-- link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.css"> -->
<link rel="stylesheet" href="public/node_modules/angular-ui-router/bower_components/ngWYSIWYG-master/dist/editor.min.css" />
 
 <link rel="stylesheet" href="public/node_modules/angular-ui-router/bower_components/angular-color-picker/dist/angularjs-color-picker.min.css" />
<link rel="stylesheet" href="public/assets/admin/css/bootstrap-datetimepicker.min.css" />

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   
  <!-- Application Dependencies -->
     <script src="public/node_modules/angular-ui-router/bower_components/angular/angular.min.js"></script>
    <script src="public/node_modules/angular-ui-router/build/angular-ui-router.js"></script>
	 <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="public/assets/frontend/js/bootstrap.min.js"></script>
	<script src="public/scripts/js/parsley.js"></script>
    <script src="public/node_modules/satellizer/satellizer.js"></script>
	<script src="public/node_modules/angular-ui-router/bower_components/oclazyload/dist/ocLazyLoad.min.js"></script>
	  
	<script src="public/node_modules/angular-ui-router/bower_components/angular-sanitize/angular-sanitize.min.js"></script>

   <script src="public/node_modules/angular-ui-router/bower_components/ngWYSIWYG-master/dist/wysiwyg.min.js"></script>

	 <script src="public/scripts/js/html2canvas.js"></script>
	 <script src="public/scripts/js/dirPagination.js"></script>
	 <script src="public/scripts/js/jquery.countdown.js"></script>
	 <script src="public/scripts/js/angular-file-upload.js"></script>
	 <script src="public/scripts/js/moment-with-locales.js"></script>
	 <script src="public/scripts/js/bootstrap-datetimepicker.js"></script>
	 <script src="public/scripts/js/bootstrap-color-picker-module.js"></script>
	
	<script src="public/scripts/js/jquery.noty.packaged.js"></script>
	<script src="public/scripts/js/jquery-ui.js"></script>
	<script src="public/assets/admin/js/sortable.js"></script>
	<script src="public/assets/admin/js/jquery.nicescroll.min.js"></script>
	<script src="public/node_modules/angular-ui-router/bower_components/ckeditor/ckeditor.js"></script>
	<script src="public/scripts/directives/custom/ng-ckeditor.js"></script>
	<script src="public/scripts/js/textAngular.min.js"></script>
	
	
<script src="public/node_modules/angular-ui-router/bower_components/angular-color-picker/dist/tinycolor.js"></script>	
<script src="public/node_modules/angular-ui-router/bower_components/angular-color-picker/dist/angularjs-color-picker.min.js"></script>
<script src="public/node_modules/angular-ui-router/bower_components/bootstrap/ui-bootstrap-tpls-0.12.1.min.js"></script>	

 	 <!--  Application Scripts -->
  
    <script src="public/scripts/app.js"></script>
    <script src="public/scripts/authController.js"></script>
    <script src="public/scripts/mainController.js"></script>
	<script src="public/scripts/factory.js"></script>
	<script src="public/scripts/interceptor.js"></script>
	<script src="public/assets/admin/js/switch.js"></script>
	<script src="public/scripts/js/angular-toggle-switch.min.js"></script>
  
	<script>

	(function($){
		
		$("#colorpicker").colorPicker();
		
	})
function fixMyRightSideDiv(){

	var w;
		 w=$('#toggleMe').find('.side-nav').outerWidth()
      $('.right-side').css('padding-left',w);
	
	}
	
  function toggleDisplay() {
			try{
				$('#toggleMe').toggle();
				if(document.getElementById("toggleMe").style.display == "none" ) {
					document.getElementById("toggleMe").style.display = "none";
					  $('.right-side').css('padding-left',0);
				}
				else {
					document.getElementById("toggleMe").style.display = "";
					fixMyRightSideDiv();
					}
			
				}catch(er){
					console.log(er.message);
				}
		}
 
  </script>
  <script type="text/javascript">
        $(function () {
   	   $('#datetimepicker12').datetimepicker({
                inline: true,
                format: 'YYYY-MM-DD HH:mm:ss',
                sideBySide: true
            })
        });
    </script>

  
  </head>
  <body ng-app="authApp">
  
 <stateloadingindicator></stateloadingindicator>
  <div class="headersavingloading"></div>
  <div ui-view></div>
 
     
     <div class="footer" ng-show="isFooterVisible()" ng-controller="mainController">
     	<div class="container">
        	
          <!--	<div class="row">
            	<div class="col-xs-12 col-md-3">
                	<a href="/" target="_self"><img src="public/assets/admin/images/launchafunnel_logo.png" alt="logo" /></a>
                </div>
   <nav class="navbar">
      <div class="container">
        <div class="footer-nav navbar-right" id="navbar">
          <ul class="nav navbar-nav">
          <li ><a href="javascript:void(0)"  target="_self">Benefits</a></li>
            <li><a href="javascript:void(0)"  target="_self">Tutorial</a></li>
            <li><a href="javascript:void(0)"  target="_self">Clients</a></li>
            <li><a href="javascript:void(0)"  target="_self">Prices</a></li>
           
          </ul>
        </div>
      </div>
    </nav>
            </div>-->
            <div class="row bottom">
            	<div class="col-xs-12">
                	<span class="copywrite">&copy; <?php echo Date('Y'); ?> - Launch A Funnel</span>
                </div>

            </div>
        </div>
     </div>
  </body>
</html>
