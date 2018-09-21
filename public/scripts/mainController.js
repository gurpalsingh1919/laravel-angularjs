'use strict';

	app.controller('mainController', mainController);

	function mainController($scope, $state,$window,$location,$stateParams) {

// This controller is use to change header/footer on the basis of pages .
   $scope.isFooterVisible = function() {
	
			var cur_Url = $location.path();
			if(cur_Url.split('/').slice(-2)[0]!='')
			{
				cur_Url = cur_Url.split('/').slice(-2)[0];
			}

			//console.log(cur_Url.split('/').slice(-2)[0]);

			 var Urls = ["/","/login", "/overview", "/myfunnels", "editmyfunnel", "/dashboard","funnelscontacts","mysettings","/usersignup","/forgotpassword","/paymentstatus","resetpassword","/adminlogin","admindashboard"]; //Array of Urls where footer will be visible..
			 if (Urls.indexOf(cur_Url) != -1){
				return true;
				}else{
					return false;
				}
		};
		
		
		
		
   }
 
