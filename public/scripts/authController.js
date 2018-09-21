'use strict';

	app.controller('AuthController', AuthController);
	
	function AuthController($scope,$rootScope,$auth,clients, $state,$window,$location,$filter,$sce) {
		$scope.funnel_cname='0';
		var urlsss = $location.host(); // e.g. "www.example.com"
		var hostnaes=urlsss.split(".").slice(-2).join("."); // "example.com"
		if(angular.isDefined($window.localStorage.access_token) && $window.localStorage.subdomain !='')
		{
			var basepath=$location.protocol() + '://' + $window.localStorage.subdomain +'.'+ hostnaes +'/';
		}
		else
			{
			var basepath=$location.protocol() + '://' +'.'+ hostnaes +'/';
			}
		CnamePath();
		function CnamePath()
		{
			if(angular.isDefined($window.localStorage.cname) && $window.localStorage.cname !='')
	  		{
	     		$scope.funnel_cname='1';
	  			var funnelpath=$window.localStorage.funnelStepPath;
	  			//var pathfunnelcnamepath=$window.localStorage.cname +'.' + $window.localStorage.subdomain +'.'+ hostnaes+'/';
	  			$scope.funnelcnamepath=$window.localStorage.cname +'/'+ funnelpath.split('/').slice(-2)[0] +'/'+funnelpath.split('/').slice(-1)[0];
	  		}
		}
		
		$scope.editStepPath=function(stepId,stepPath){
			
			$scope.errormessage='';
			  $scope.submitted_path='';
			$scope.editsteppath=stepPath;
			$scope.editsteppathid=stepId;
			 var elementEditFunnelStepPath = angular.element('#editFunnelStepPath');
			 elementEditFunnelStepPath.modal('show');
		}
		
		
		
			$scope.checkStepTemplate=function(templateid,stepname,stepid,slug,contacts,visitors,tempcode){
			$scope.stepName=stepname;
			$scope.stepId=stepid;
			$scope.template_id=templateid;
			
			$scope.funnelStepPath=basepath+tempcode+'/' + slug;
			//*************** set data in local storage ***********************//
			$window.localStorage.stepname=$scope.stepName;
			$window.localStorage.step_id=$scope.stepId;
			$window.localStorage.template_id=$scope.template_id;
			$window.localStorage.funnelStepPath=$scope.funnelStepPath;
			CnamePath();
			
		}
		if($state.is('editmyfunnel'))
	 	{
	        	 var getterFunnelI = $location.absUrl().split('/').pop();
	        	 var funnelId=$window.localStorage.funnel_id;
	        	 if(getterFunnelI !=funnelId)
        		 {
	        		 $window.localStorage.notDefault='0';
	        		 $location.path('/myfunnels').replace();
        		 }
	        	 else if(angular.isDefined(funnelId))
        		 {
	        		 getFunnelsTemplate(funnelId);
        		 }
	        	getCurrentUserDetail();
	        	
	 	}
		if($state.is('myfunnels'))
	 	{
			$window.localStorage.notDefault='0';
			$window.localStorage.funnel_id="";
	        $window.localStorage.step_id="";
	        $window.localStorage.template_id="";
	        $window.localStorage.stepname="";
			$window.localStorage.funnelStepPath="";
			$window.localStorage.cname="";
			$window.localStorage.stateChange="";
			getMyAllFunnels();
	 	}
		if($state.is('usersignup') || $state.is('home'))
	 	{
			$scope.activeclass='usersignup';
			var data = [];
			var Url = 'payment/getpaymentplans'
			var email_chk = clients.getTemplateData(data,Url).then(function(response) {
				$scope.planArr=response.data.data.plans;
			});
			
	 	}
		
		if($state.is('paymentstatus'))
		{
			UserpaymentStatus();
		}
		function UserpaymentStatus()
		{
			var usertoken=$window.localStorage.access_token;
			var data = {token:usertoken};
			var Url = 'api/getUserDetail'
			var email_chk = clients.getTemplateData(data,Url).then(function(successCallback) {
				//$scope.planArr=response.data.data.plans;
				if(successCallback.data.status=="success")
 				{	console.log(successCallback.data.data.user.subdomain);
					if(angular.isDefined(successCallback.data.data.user.subdomain) && successCallback.data.data.user.subdomain !=''){
         				$window.localStorage.subdomain=successCallback.data.data.user.subdomain;
         				}
         				else
         					{
         					$window.localStorage.subdomain='';
         					}
     				
	     			var userstatus = successCallback.data.data.user.status;
					if(userstatus=='1')
					{
						$location.path('/dashboard').replace();
					}
					else
					{
						var data = [];
						var Url = 'payment/getpaymentplans'
						var email_chk = clients.getTemplateData(data,Url).then(function(response) {
							$scope.planArr=response.data.data.plans;
						});
						$location.path('/paymentstatus').replace();
					}
 				}
				else
				{
					 $scope.errormessage=successCallback.data.data.message;
    				 generate('error',successCallback.data.data.message);
    				 setTimeout(hideSuccessMessage ,3000);
    				// $state.go('userauth');
    				 $location.path('/login').replace();
				}
				
				
			});
		}
		$scope.dataDismiss=function(dataname){
			if(dataname='funnel')
			{
				$scope.funnelName='';
			}
			if(dataname='funnelstep')
			{
				$scope.funnelStepName='';
				$scope.funnelStepPath1='';
			}
			
		}

		$scope.makePayment=function(planName,price,planvalidity,plans_id){
			//console.log("i am in payment");
			var usertoken=$window.localStorage.access_token;
			var data ={token: usertoken,plan_name:planName,cost:price,validity:planvalidity,plan_id:plans_id};
			var Url = 'payment/repayment'
			var email_chk = clients.getTemplateData(data,Url).then(function(response) {
				console.log(response);
				 if(response.data.status=="success")
 				 {
     				 
     				if(angular.isDefined(response.data.data))
  	      			{
     					$window.sessionStorage.paypaldataArr=response.data.data.paypaldata; 
     					//$window.localStorage.access_token =  response.data.data.token;
  	        		}
  	      			else
  	      			{
  	      			
  	      				var parsdata=JSON.parse(response.data);
  	      			$window.sessionStorage.paypaldataArr=parsdata.data.paypaldata;
  	      				//$window.localStorage.access_token =  parsdata.data.token;
  	      			}
     				
     				 $location.path('/paypal').replace();
 				 }
			});
		}

		$scope.signup=function(){
			
				$location.path('/usersignup').replace();
		}
		$scope.loginuser=function(){
		
			 $location.path('/login').replace();
		}
		/*$scope.adminlog=function()
		{ 
		     $location.path('/adminlogin').replace();
		}*/
		 

		//*****Add Active class********//
			
			if($state.is('userauth'))
			{
				$scope.activeclass='login';
			}
			if($state.is('usersignup'))
			{
				$scope.activeclass='usersignup';
			}
			/*if($state.is('adminlogin'))
			{
				$scope.activeclass='adminlogin';
			}*/

		$('#login_form').parsley();
		$('#password_change_form').parsley();
		$('#edituserform').parsley();


		var vm = this;
		//$scope.submitted = true;
		$scope.success_message = '';
		// This controller is use to change header/footer on the basis of pages .
		 //***********Login function ********************//
		$scope.login = function() {
		
			var credentials = {
				email: $scope.email,
				password: $scope.password
			}
			
            	$scope.error = {};
				$("#login_btn").attr('disabled', 'disabled');
				$("#login_btn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
            	// Use Satellizer's $auth service to login
				$auth.login(credentials).then(function(response) {
					//console.log(response.data.error);
				$window.localStorage.access_token = response.data.token;
				// If login is successful, redirect to the users state
				//$state.go('dashboard');

				UserpaymentStatus();
				//$location.path('/paymentstatus').replace();

				 $location.path('/dashboard').replace();

				
			}, function(error) {
			$("#login_btn").removeAttr('disabled');
			$("#login_btn").html('Login');
				vm.error = error;
			});
		}
	
		//****** Forgot Password function ********************//
		$('#forgotpassword_form').parsley();
		$scope.forgotpassword = function() {
			$("#emailloader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/loader-img.gif"></div>');
			var email = $scope.email_id;
			var data = {email_id: email};
			var Url = 'api/checkForgotEmail';
			var email_chk = clients.getTemplateData(data,Url).then(function(response) {
				if (response.data.status == 'success') 
				{
					$("#emailloader").html('');
					$scope.emailsuccess = response.data.data.message;
					$scope.emailvalid="";
				}
				else
				{
					$("#emailloader").html('');
        			generate('error',response.data.data.message);
        			$scope.emailvalid = response.data.data.message;
        			$scope.emailsuccess = "";
					setTimeout(hideSuccessMessage ,3000);
					return false;
				}
			});	
		}
		$scope.emailvalid="";
		//*************Reset Password Functionality**************//
			$scope.resetpassword = function() 
			{
				$("#emailloader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/loader-img.gif"></div>');
				var resettoken = $location.absUrl().split('/').pop();
				$('#resetpassword_form').parsley();
				var resetpassword = $scope.reset_new_password;
				var resetconfirmpassword = $scope.reset_confirm_password;
				if(resetpassword != resetconfirmpassword)
	        	{
					$("#emailloader").html('');
					generate('error',"Password Does not Match");
					setTimeout(hideSuccessMessage ,3000);
	        		$scope.reset_pass_error_message="Password Does not Match";
	        		return false;
	        	}
				else
				{
					$scope.reset_pass_error_message="";
					var data={pass: resetpassword,token: resettoken};
		    		var Url="api/resetPassword";
		    		var updated_password = clients.getTemplateData(data, Url).then(function(response){
		    			if (response.data.status == 'success') {
		    				$("#emailloader").html('');
		    				$scope.reset_pass_success_message=response.data.data.message;
		    				generate('success',response.data.data.message);
		    				setTimeout(hideSuccessMessage ,3000);
		    			}
		    			else
		    			{
		    				$("#emailloader").html('');
		    				$scope.reset_pass_error_message=response.data.data.message;
		    				generate('error',response.data.data.message);
		    				setTimeout(hideSuccessMessage ,3000);
		    			}
		    		});
				}
			}
		//************** Logout functionality ********************//
			 $scope.logout = function() {
	             delete $window.localStorage.access_token;
	             RedirectToLoginIfTokenNotExist();
	         };
	         $scope.adminlogout = function() {
	             delete $window.localStorage.access_token;
	             $location.path('/admin').replace();
	         };
         //***************** Sign up functionality ********************//
         $scope.paypaldataArr =[];
        $('#register_form').parsley();
         $scope.register=function(){
        	
        	 var credentials = {
      				first_name: $scope.first_name,
      				last_name: $scope.last_name,
      				email: $scope.email,
      				password: $scope.password,
      				password_confirmation: $scope.confirm_paswd,
      				contact_no: $scope.phone_no,
      				payment_plan:$scope.payment_plan
      				
      			}
        	 if($scope.password != $scope.confirm_paswd)
  			{
      			generate('error','Password and confirm password should be the same.');
 					setTimeout(hideSuccessMessage ,3000);
 					return false;
  			}
        	 
        	 var Url="payment/userRegistration";
        	 registeration(credentials,Url)

         }
         function registeration(credentials,Url)
         {
        	 $scope.loadingModel = true;
        	 	var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
        	 	 if(response.data.status=="success")
 				 {
     				 
     				if(angular.isDefined(response.data.data))
  	      			{
     					//console.log("if");
     					$window.sessionStorage.paypaldataArr=response.data.data.paypaldata; 
     					$window.localStorage.access_token =  response.data.data.token;
     					//console.log(response.data.data.token);
     					
  	        		}
  	      			else
  	      			{
  	      				//console.log("else");
  	      				var parsdata=JSON.parse(response.data);
  	      				$window.sessionStorage.paypaldataArr=parsdata.data.paypaldata;
  	      				$window.localStorage.access_token =  parsdata.data.token;
  	      				//console.log(parsdata.data.token);
  	      			}
     				
     				$location.path('/paypal').replace();
 				 }
     			 else
 				 {
     				 $scope.errormessage=response.data.data.message;
     				 generate('error',response.data.data.message);
     				 setTimeout(hideSuccessMessage ,3000);
     				 //$state.go('usersignup');
     				$location.path('/usersignup').replace();
 				 }
     			$scope.loadingModel = false;
     			

     		 });

         }
         
        if($state.is('paypal'))
 	 	{
        	$scope.paypaldataArr= $window.sessionStorage.paypaldataArr;
        	$("#test").html($scope.paypaldataArr);
        	
    	 }
        else
    	{
        	$window.sessionStorage.paypaldataArr='';
    	}
        if(angular.isDefined($window.localStorage.access_token) && ( !$state.is('paypal') || !$state.is('admindashboard')))
		{
        	var usertoken=$window.localStorage.access_token;
			var data = {token:usertoken};
			var Url = 'api/getUserStatus'
			var email_chk = clients.getTemplateData(data,Url).then(function(response) {
				//console.log(response.data.status);
				 if(response.data.status=="success")
 				 {
					 $location.path('/paymentstatus').replace();
 				 }
			});
		}
       
       // $scope.emailsuccess=$window.localStorage.emailsuccess;
        function RedirectToLoginIfTokenNotExist()
        {
        	if(!angular.isDefined($window.localStorage.access_token) || $window.localStorage.access_token=='')
    		{
    			$location.path('/login').replace();
    		}
        	$location.path('/login').replace();
        }
         $scope.createFunnelFromTemplate=function()
         {
        	 var elementmyfunnel = angular.element('#myFunnel');
        	 elementmyfunnel.modal('show');
         }
       
       
         $scope.overviewView=function(){
        	 $scope.loadingModel=true; 
        		if($scope.funnelName=='' || !angular.isDefined($scope.funnelName) ){
        			generate('error','You must enter the funnel name.');
    					setTimeout(hideSuccessMessage ,3000);
    					return false;
    				}
        		
        		var funnelName=$scope.funnelName;
        		var funnelGroupName=$scope.funnelGroupName;
        		var data={funnel_name : funnelName,group_name: funnelGroupName,token:$window.localStorage.access_token};
        		
        		var Url="admin/addNewFunnel";
        		 var aweber_data = clients.getTemplateData(data, Url).then(function(response) {
        			if (response.data.status == 'Success') {
 						var slug_url=response.data.data.slug;
 						$(".modal-backdrop").fadeOut();
 			        	$("body").removeClass('modal-open');
 			        	 var funnel_id=response.data.data.funnel_id;
 			        	$window.localStorage.funnel_name=response.data.data.funnel_name;
 			        	$window.localStorage.funnel_id=funnel_id;
 			        	getFunnelsTemplate(funnel_id);
 			        	
 			        	if($state.is('editmyfunnel'))
		        		{
 			        		 $window.location.reload();
		        		}
 			        	else
		        		{
 			        		 $location.path('/editmyfunnel/'+response.data.data.funnel_id);
		        		}
 			        	
 					}
 					else if (response.data.status == 'Fail') {
 						generate('error',response.data.data.message);
 						$location.path('/login').replace();
 						}
        			$scope.loadingModel=false;
 			  });
        		         	
        }
         
         $scope.funnel_name=$window.localStorage.funnel_name;

        
        //******************** Funnel steps functionality *****************************//
         var tmpList = [];
         function getFunnelsTemplate(funnelId)
         {
        	  var Url = 'admin/funnelAlltemplates';
        	  var data={funnel_id : funnelId};
              var template_data = clients.getTemplateData(data, Url).then(function(response) { 
      			if (response.data.status == 'success') 
      			{
      				tmpList=response.data.data.TempArr;
      				$window.localStorage.funnel_name=response.data.data.funnel_name;
      				$scope.list = tmpList;
      				//****************** set first step data *************************//
      				 var getterFunnelI = $location.absUrl().split('/').pop();
      				if($window.localStorage.notDefault == '1')
  					{
      					setSelectedStepDataInLocalStorage();
  					}
      				else
  					{
      					$scope.template_id=tmpList[0]['template_id'];
          				$scope.stepName=tmpList[0]['name'];
          				$scope.stepId=tmpList[0]['id'];
          				var slug=tmpList[0]['slug'];
          				var temp_code=tmpList[0]['temp_code']
          				$scope.funnelStepPath=basepath+temp_code+'/' + slug;
          				//getfunnelstepPath(slug);
          				
          				$window.localStorage.funnel_id=funnelId;
 			        	$window.localStorage.step_id=$scope.stepId;
 			        	$window.localStorage.template_id=$scope.template_id;
 			        	$window.localStorage.stepname=$scope.stepName;
 						$window.localStorage.funnelStepPath=$scope.funnelStepPath;
 						CnamePath();
  					}
      				  				
      			} 
      			else 
      			{
      				generate('error',response.data.data.message);
 				 	setTimeout(hideSuccessMessage ,3000);
 				 	$location.path('/login').replace();
      			}
      			
      			
      		});
         }
        
		  
         //******************** Funnels Contact functionality *****************************//
        $scope.backToMyEditedFunnel=function()
 		{
        	var slug_url= $location.absUrl().split('/').pop(); 
 			var redirecturl="/editmyfunnel/"+slug_url;
 			$location.path(redirecturl).replace();
 		}
        $scope.getFunnelSettings = function()
        { 
        	console.log('settings');
        	getFunnelDetails();
        } 
        if($state.is('funnelsettings')){
        	getFunnelDetails();
        }
        function getFunnelDetails()
        {
        	  var getterFunnelID = $location.absUrl().split('/').pop(); 
        	  var Url = 'admin/funnelDetails'; 
           	  var data={funnel_id:getterFunnelID,token:$window.localStorage.access_token};
           	 var template_data = clients.getTemplateData(data, Url).then(function(successCallback) { 
           	if(successCallback.data.status=='Fail')
   			  {
       			  		generate('error',successCallback.data.data.message);
    				 	setTimeout(hideSuccessMessage ,3000);
    				 	$location.path('/myfunnels').replace();
   			  }
            else
			  { 
	    			if(angular.isDefined(successCallback.data.data))
	      			{
	        			  var funneldetail=successCallback.data.data.funneldetail; 
	        		}
	      			else
	      			{
	      				 var parsdata=JSON.parse(successCallback.data);
	      				 var funneldetail=parsdata.data.funneldetail;
	      				
	      			}
	    			
	    			angular.forEach(funneldetail,function(val,key){
    					 $scope[key]=val;
    				});
	    			$window.localStorage.cname=funneldetail.cname;
	    			 //$scope.funnel_name=funneldetail.funnel_name;
      				// $scope.funnel_cname=funneldetail.cname;
	    			$location.path('/funnelsettings/'+getterFunnelID).replace();	
			  }   
           		
            });
        }
        $("#funnel_setting").parsley();
        $scope.updatefunnelDetail =function(){
        	if($scope.funnel_setting.$invalid)
			 { //console.log('if');
				 return false;
			 }
        	console.log("i am in funnel");
        	console.log($scope.funnel_name);
        	console.log($scope.cname);
        	
        	 var getterFunnelID = $location.absUrl().split('/').pop(); 
       	  	var Url = 'admin/updatefunnelDetails'; 
          	  var data={funnel_name:$scope.funnel_name,funnel_cname:$scope.cname,funnel_id:getterFunnelID,token:$window.localStorage.access_token};
          	 var template_data = clients.getTemplateData(data, Url).then(function(successCallback) { 
          		if(successCallback.data.status=='Fail')
     			  {
          			$scope.error_message=msg;
         			  		generate('error',successCallback.data.data.message);
      				 	setTimeout(hideSuccessMessage ,3000);
      				 	$location.path('/myfunnels').replace();
     			  }
              else
  			  { 
  	    			if(angular.isDefined(successCallback.data.data))
  	      			{
  	        			  var funneldetail=successCallback.data.data.funneldetail; 
  	        			  var msg=successCallback.data.data.message;
  	        		}
  	      			else
  	      			{
  	      				 var parsdata=JSON.parse(successCallback.data);
  	      				 var funneldetail=parsdata.data.funneldetail;
  	      				 var msg=parsdata.data.message;
  	      				
  	      			}
  	    			$window.localStorage.cname=$scope.cname;
  	    			$window.localStorage.funnel_name=$scope.funnel_name;
  	    			$scope.success_message=msg;
  	    			angular.forEach(funneldetail,function(val,key){
      					 $scope[key]=val;
      				});
  	    			generate('success',msg);
  				 	setTimeout(hideSuccessMessage ,3000);
  				 	
  			  }
           });
        }
         $scope.getFunnelsContacts = function()
         { 
	        	getFunnelsContacts();
         } 
         if($state.is('funnelscontacts')){
	        	getFunnelsContacts();
         }
        
         function getFunnelsContacts()
         { 
          var getterFunnelID = $location.absUrl().split('/').pop(); 
       	  var Url = 'admin/funnelAllContacts'; 
       	  var data;
       	  if(angular.isDefined($scope.startDate) && angular.isDefined($scope.endDate))
       	  {
       		var date1 = $scope.startDate;
        	var date2 = $scope.endDate;
        	var startDate=date1.split('/');
        	var finalStartDate=startDate[2]+'-'+startDate[1]+'-'+startDate[0];
        	var endDate=date2.split('/');
        	var finalEndDate=endDate[2]+'-'+endDate[1]+'-'+endDate[0];
        	
       		data={funnel_id : getterFunnelID,start_date:finalStartDate, end_date:finalEndDate};
       	  }
       	  else
       	  {
       		var finalStartDate='';
       		var  finalEndDate='';
       		data={funnel_id : getterFunnelID,start_date:finalStartDate, end_date:finalEndDate};
       	  }    	  
          var template_data = clients.getTemplateData(data, Url).then(function(successCallback) { 
        	  if(successCallback.data.status=='Fail')
			  {
    			  	generate('error',successCallback.data.data.message);
 				 	setTimeout(hideSuccessMessage ,3000);
 				 	$location.path('/login').replace();
			  }
    		  else
			  { 
	    			if(angular.isDefined(successCallback.data.data))
	      			{
	        			  $scope.contactsArr=successCallback.data.data.contactArr; 
	        		}
	      			else
	      			{
	      				 var parsdata=JSON.parse(successCallback.data);
	      				 $scope.contactsArr=parsdata.data.contactArr;
	      			}
	    			
	    			$location.path('/funnelscontacts/'+getterFunnelID).replace();	
			  }      		
      			$scope.sort = function(keyname){
      			$scope.sortKey = keyname;   //set the sortKey to the param passed
      			$scope.reverse = !$scope.reverse; //if true make it false and vice versa
      		}
               
        	 
         });
         }
         
         
         function setSelectedStepDataInLocalStorage()
  		{
  			$scope.stepName=$window.localStorage.stepname;
  			$scope.stepId=$window.localStorage.step_id;
  			$scope.template_id=$window.localStorage.template_id;
  			$scope.funnelStepPath=$window.localStorage.funnelStepPath;
  			CnamePath();
  			
  		}

         
        //*************************** show and hide popup *****************************//
         $scope.showFunneltype=function(status)
         {
        	 var elementFunnelType = angular.element('#selectFunnelType');
        	 var elementNewFunnel = angular.element('#createNewFunnel');
        	 var elementFunnelStep = angular.element('#createNewFunnelStep');
        	
        	 if(status=='1')
    		 {
        		elementNewFunnel.modal('hide');
        		elementFunnelType.modal('show');
    		 }
        	 else if(status=='2')
    		 {
        		 elementFunnelType.modal('hide');
        		 elementNewFunnel.modal('show');
    		 }
        	 else if(status=='3')
    		 {
        		 elementFunnelStep.modal('show');
    		 }
        	 
        	        	// $scope.createNewFunnel=true;
         }
       //******************************** Show all funnels ****************************************************//
    	 function getMyAllFunnels()
    	 { 
    		 $scope.loading=true;
    		  var Url='admin/allFunnelsOfUser';
        	 var data={token:$window.localStorage.access_token};
        	  $scope.filteredFunnel = []
        	  var funneldata = clients.getTemplateData(data, Url).then(function(successCallback) {
        		  if(successCallback.data.status=='Fail')
    			  {
        			  	generate('error',successCallback.data.data.message);
     				 	setTimeout(hideSuccessMessage ,3000);
     				   RedirectToLoginIfTokenNotExist();
    			  }
        		  else
    			  { 
		    			if(angular.isDefined(successCallback.data.data))
		      			{
		        			  $scope.funnelArr=successCallback.data.data.funnelArr;
		        		}
		      			else
		      			{
		      				 var parsdata=JSON.parse(successCallback.data);
		      				 $scope.funnelArr=parsdata.data.funnelArr;
		      			}
    			  }
        		 //console.log($scope.funnelArr);
        		  $scope.loading=false;
        			$scope.sort = function(keyname){
              			$scope.sortKey = keyname;          //set the sortKey to the param passed
              			$scope.reverse = !$scope.reverse; //if true make it false and vice versa
              		}      			
      			
        	  });
        	 
        	
    	 }
    	 
    	
    //**************************** Funnels editing functionality ****************************************//
    	  $scope.funneUpdate=function(funnelid,funnelname,funnel_cname){
    		  console.log(funnel_cname);
    		  $scope.funnelName=funnelname;
    		  $window.localStorage.funnel_name=funnelname;
    		  $window.localStorage.cname=funnel_cname;
    		  $window.localStorage.funnel_id=funnelid;
    		  $location.path('/editmyfunnel/'+funnelid);
    	  }
   //**************************** Funnels Delete functionality ****************************************//
    	  $scope.deleteFunnel=function(funnelid,funnelname){
    		  var Url = 'admin/deletdMyCompletefunnel';
        	  var data={funnel_id : funnelid};
        	  clients.getTemplateData(data, Url).then(function(response) {
        		if(response.data.status=='success')
        		{
        			  for(var i = $scope.funnelArr.length - 1; i >= 0; i--){
        					if($scope.funnelArr[i].id == funnelid){
        						$scope.funnelArr.splice(i,1);
        					}
        				}
        			  getMyAllFunnels();
        				 generate('success',response.data.data.message);
        				 setTimeout(hideSuccessMessage ,3000);
        		}
        		else
    			{
        			 generate('error',response.data.data.message);
    				 setTimeout(hideSuccessMessage ,3000);
    				 $location.path('/login').replace();
    			}
        		 	
        		  
        	  });
    	
    	  }
    //******************************* add template into step      **************************************//
    	 $scope.addTemplateIntoStep=function(templateid,temp_code,image,temp_slug){
    		 var funnelstepid=$scope.stepId;
    		  var Url = 'admin/AddTemplateInFunnelStep';
        	  var data={template_id : templateid,funnel_step_id:funnelstepid};
        	  clients.getTemplateData(data, Url).then(function(response) {
        		  	if(response.data.status=='success')
	          		{
			    		  for(var i = 0; i < $scope.list.length; i++){
			    			 if($scope.list[i].id == funnelstepid){
			    				 
									$scope.list[i].template_id=templateid;
									$scope.list[i].temp_code=temp_code;
									$scope.list[i].temp_slug=temp_slug;
									$scope.list[i].contacts='0';
									$scope.list[i].visitors='0';
									$scope.list[i].image=image;
									$scope.template_id=templateid;
									$scope.temp_code=temp_code;
									
									$window.localStorage.template_id=$scope.template_id;
						  			
					 				}
							}
			    			var slug=response.data.data.slug;
			    			$scope.funnelStepPath=basepath+$scope.temp_code+'/' +slug;
			    			$window.localStorage.funnelStepPath=$scope.funnelStepPath;
			    			CnamePath();
	          		}
        		  else
				  {
        			  generate('error',response.data.data.message);
     				 setTimeout(hideSuccessMessage ,3000);
     				$(".modal-backdrop").fadeOut();
			        	$("body").removeClass('modal-open');
     				$location.path('/login').replace();
				  }
        	  });
          }
    //****************************** Delete step or template from funnel *******************************//
    	 $scope.deleteStepFromFunnel=function(){
    		 
			var answer = confirm("Are you sure you want to delete this template ?")
			if (answer) 
			{
    		 	var funnelstepid=$scope.stepId;
    		 	var Url = 'admin/DeleteTemplateFromFunnel';
    		 	var data={funnel_step_id:funnelstepid};
    		 	clients.getTemplateData(data, Url).then(function(response) 
    		 	{
	        		  	if(response.data.status=='success')
		          		{
		        		  	  for(var i = $scope.list.length - 1; i >= 0; i--){
		      					if($scope.list[i].id == funnelstepid){
		      						$scope.list.splice(i,1);
		      					}
		      				}
	        		  	  	var tmpList=$scope.list;
		        		  	$scope.template_id=tmpList[0]['template_id'];
		      				$scope.stepName=tmpList[0]['name'];
		      				$scope.stepId=tmpList[0]['id'];
		      				var slug=tmpList[0]['slug'];
		      				var temp_code=tmpList[0]['temp_code'];
		      				$scope.funnelStepPath=basepath+temp_code+'/' +slug;
		      				//getfunnelstepPath(slug);
		      				
	      					$window.localStorage.funnel_id=funnelId;
				        	$window.localStorage.step_id=$scope.stepId;
				        	$window.localStorage.template_id=$scope.template_id;
				        	$window.localStorage.stepname=$scope.stepName;
							$window.localStorage.funnelStepPath=$scope.funnelStepPath;
							CnamePath();
							generate('success',response.data.data.message);
							setTimeout(hideSuccessMessage ,3000);
	        		  		
		          		}
	        		  	else
	        		  	{
	        		  		generate('error',response.data.data.message);
	        		  		setTimeout(hideSuccessMessage ,3000);
	        		  		$location.path('/login').replace();
	        		  	}
        	  });
			}
    	 }
     //****************************** Drag and drop functionality **************************************//
         $scope.sortingLog = [];
         $scope.sortableOptions = {
                     stop: function(e, ui) {
                     var logEntry = tmpList.map(function(i){
                    	
                    return i.id;
             }).join(', ');
                     stepSorting(logEntry);
            $scope.sortingLog.push('Update: ' + logEntry);
           },
         };
         function stepSorting(sortedList){
        	  var Url = 'admin/sortFunnelStep';
        	  var data={sorted_list : sortedList};
        	  var funnelstep_data = clients.getTemplateData(data, Url).then(function(response) {
           			
           		});
         }
    //******************************* Save new funnel step *********************************************//  
       $scope.saveNewFunnelStep = function(){
    	   var Url = 'admin/addfunnelstep';
    	   var elementFunnelStep = angular.element('#createNewFunnelStep');
    		if($scope.funnelStepName=='' || !angular.isDefined($scope.funnelStepName) ){
    			generate('error','You must enter the funnel step name.');
					setTimeout(hideSuccessMessage ,3000);
					return false;
				}
    	   var funnelId=$window.localStorage.funnel_id;
    	   var stepName= $scope.funnelStepName;
    	   var millisecondsCurrentDate = new Date().getTime();
    	   if($scope.funnelStepPath1=='' || !angular.isDefined($scope.funnelStepPath1) )
    		{
    	  
    		  var stepNames = stepName.replace(/\s+/g, "-");
    		  var stepPath=stepNames+'-'+millisecondsCurrentDate;
		   }
    	   else
		   {
    		   var PathName = $scope.funnelStepPath1.replace(/\s+/g, "-");
    		   var stepPath=PathName+'-'+millisecondsCurrentDate;
		   }
    	
    	 var data={funnel_id : funnelId,funnelStepName:stepName,path:stepPath,token:$window.localStorage.access_token};
    	   var funnelstep_data = clients.getTemplateData(data, Url).then(function(response) { 
 			if (response.data.status == 'Success') 
 			{
 				var tempid='0';
 				$scope.list.push({id:response.data.data.id,name:response.data.data.stepName,sort_order:response.data.data.sort_order,template_id:tempid,slug:response.data.data.stepPath});
 				
 				$scope.stepId=response.data.data.id;
 				$scope.template_id=tempid;
 				
 				$scope.stepName=response.data.data.stepName;
 				$scope.funnelStepPath=basepath+tempid+'/'+response.data.data.stepPath;
 				//var slug=response.data.data.stepPath;
 				//getfunnelstepPath(slug);
 				//**************************** Set data in local storage **************
 				$window.localStorage.stepname=$scope.stepName;
 				$window.localStorage.step_id=$scope.stepId;
 				$window.localStorage.template_id=$scope.template_id;
 				$window.localStorage.funnelStepPath=$scope.funnelStepPath;
 				CnamePath();
 				generate('success',"Funnel step added successfylly");
				setTimeout(hideSuccessMessage ,3000);
 				elementFunnelStep.modal('hide');
 				
 				$scope.funnelStepPath1='';
 				$scope.funnelStepName ='';
 			} 
 			else 
 			{
 				generate('error',response.data.data.message);
				setTimeout(hideSuccessMessage ,3000);
 				elementFunnelStep.modal('hide');
 				$location.path('/login').replace();
 			}
     			
     			
     		});
       }
       /*********************** Edit funnel step Path *********************/
       $scope.saveFunnelStepPath=function(){
    	   if($scope.editsteppath=='' || !angular.isDefined($scope.editsteppath) ){
    		   		$scope.errormessage="You must enter the funnel step path.";
   					generate('error','You must enter the funnel step path.');
					setTimeout(hideSuccessMessage ,3000);
					return false;
				}
    	   if($scope.submitted_path){
    	   var path_slug=$scope.editsteppath;
    	   var Url="admin/updatefunnelStepPath";
    	   var data={step_id : $scope.editsteppathid,funnelStepPath:path_slug,token:$window.localStorage.access_token};
    	   var funnelstep_data = clients.getTemplateData(data, Url).then(function(response) { 
 			if (response.data.status == 'success') 
 			{
 				 for(var i = 0; i < $scope.list.length; i++){
	    			 if($scope.list[i].id == $scope.editsteppathid){
	    				 	$scope.list[i].temp_slug=path_slug;
	    				 	var temp_code=$scope.list[i]['temp_code'];
			 				}
					}
	    			
	    			$scope.funnelStepPath=basepath+temp_code+'/' +path_slug;
	    			$window.localStorage.funnelStepPath=$scope.funnelStepPath;
	    			CnamePath();
	    			
 				generate('success',response.data.data.message);
				setTimeout(hideSuccessMessage ,3000);
				//elementEditFunnelStepPath.model('hide');
 			} 
 			else 
 			{
 				generate('error',response.data.data.message);
				setTimeout(hideSuccessMessage ,3000);
 				//elementFunnelStep.modal('hide');
 				//$location.path('/login').replace();
 			}
     			
     			
     		});
       }
       }
       
       //***************** Find the unique path **********************//
       $scope.checkStepPath= function(path){
    	   $scope.submitted_path='';
      	if(angular.isDefined(path) && path !=''){
      		//alert(subdomain);
      		var data={token: $window.localStorage.access_token,step_path:path};
       		var Url="admin/checkYourStepPath";
       		var updated_domain = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
       			//console.log(successCallback);
       			if(successCallback.data.status=="success")
   				{
       				$scope.submitted_path=false;
       				$scope.errormessage="Path is already exist";
       				
       			}
       			else if(successCallback.data.status=="successwithnoerro")
   				{
       				$scope.submitted_path=true;
       				$scope.errormessage='';
       				//$scope.frmdomainsetting.subdomain.$setValidity("unique", true); 
   				}
       			else
   				{
       				$scope.submitted_path=false;
       				 $scope.errormessage=successCallback.data.data.message;
      				 generate('error',successCallback.data.data.message);
      				 setTimeout(hideSuccessMessage ,3000);
      				// $location.path('/login').replace();

   				}
       			
       		});
      	}
      	      	
      	
       }
      // $scope.tempArrData.push({id:response.data.data.id,name:response.data.data.stepName,sort_order:response.data.data.sort_order});
      
         
         $scope.select=function(id, name)
         { 
        	
	         var template_id=id; 
	         $scope.slug_url=name;
	         $window.localStorage.step_id=$scope.stepId;
	         $location.path('/'+$scope.slug_url+'/'+ template_id).replace();
	         //$state.go($scope.slug_url,{'id':template_id}, { notify: false });
	         setTimeout(function() {
	            $window.location.reload();
	         }, 500);
	         //$state.forceReload();
	       //  $stateParams={'id':template_id};
	      //$state.go($scope.slug_url, {'id':template_id}, { 
	        	// reload: true, inherit: false, notify: false 
	          // });
	        	 //$state.forceReload();
	      
	        
	        // $location.url('/'+$scope.slug_url+'/'+ template_id);
	    }
         $scope.mysettings=function(){
        	
        	 $location.path("/mysettings").replace();
         }
        
         $scope.preview=function(id,name,step_id)
         { 
        	 var urlsss = $location.host(); // e.g. "www.example.com"
     		var hostnaes=urlsss.split(".").slice(-2).join("."); // "example.com"
     		if(angular.isDefined($window.localStorage.access_token) && $window.localStorage.subdomain !='')
    		{
    			var basepath=$location.protocol() + '://' + $window.localStorage.subdomain +'.'+ hostnaes +'/';
    		}
    		else
    			{
    			var basepath=$location.protocol() + '://' +'.'+ hostnaes +'/';
    			}
     		//var basepath=$location.protocol() + '://' + $window.localStorage.subdomain +'.'+ hostnaes +'/';
     		
        	//var basepath=$location.protocol() + '://' + $window.localStorage.subdomain +'.' + $location.host()+'/';
        	//var basepath=$location.protocol() + '://' + $location.host()+'/';
        	var preview_url='preview/'+name+'/'+id;
        	var url = basepath+preview_url;
        	window.open(url,'_blank'); 
         } 
         $scope.createdFunnel=function()
         { 
        	var url = $scope.funnelStepPath;
        	window.open(url,'_blank');
         }
         
       
         
         

         function getCurrentUserDetail(){
        	 var userDetail;
 			var data={token: $window.localStorage.access_token};
     		var Url="api/getUserDetail";
     		var updated_password = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
     			console.log(successCallback.data.data.user.subdomain);
     			//console.log(successCallback.data.data.user.email);
     			if(successCallback.data.status=="success")
 				{	
     				//userDetail = successCallback.data.data.user;
     				$scope.userdetail = successCallback.data.data.user;
     				angular.forEach(successCallback.data.data.user,function(val,key){
     					 $scope[key]=val;
     				});
     				if(angular.isDefined(successCallback.data.data.user.subdomain) && successCallback.data.data.user.subdomain !=''){
         				$window.localStorage.subdomain=successCallback.data.data.user.subdomain;
         				}
         				else
         					{
         					$window.localStorage.subdomain='';
         					}
     				//$window.localStorage.subdomain=successCallback.data.data.user.subdomain;
     				//console.log(successCallback.data.data.user.subdomain);
     				$scope.phone=parseInt(successCallback.data.data.user.contact_no);
     				$scope.setloading=false;			
 				}
     			else
 				{
     				 $scope.errormessage=successCallback.data.data.message;
    				 generate('error',successCallback.data.data.message);
    				 setTimeout(hideSuccessMessage ,3000);
    				// $state.go('userauth');
    				 $location.path('/login').replace();

 				}
     			
     		});
          }
         //*************** Request all template paths ********************//
      /*   $window.localStorage.templateurls=[];
         var requesturl="admin/getAllTemplatePath";
 		 var data=[];
 		 var updated_password = clients.getTemplateData(data, requesturl).then(function(successCallback,errorCallback){
 			 if(successCallback.data.status=="success")
 				{	
 				 //console.log(successCallback.data.data.template_path);
 				$window.localStorage.templateurls=successCallback.data.data.template_path;
 				}
 		 });*/
 		

	} 
	//********************Search Functionality*********************//
	function searchUtil(item, toSearch) {
		  /* Search Text in all 3 fields */
		  return (item.name.toLowerCase().indexOf(toSearch.toLowerCase()) > -1) ? true : false;
		}

	
 //************************************* Authorization function ************************//	
	var _CURR_PARENT_MENU = '/';
	app.factory("MyAppAuthIn", ['$window', '$q','$location',function ($window, $q,$location) {
		
		
         return {

					
        	 request: function (config) {
        		// var template_url=[];
        		// template_url=$window.localStorage.templateurls;
        		
        	       var cur_Url = $location.absUrl().split('/').slice(-2)[0];
                   var Urls = ["5850", "5851", "5852", "5853", "5854","5860","5861","5862","5863","resetpassword"]; //Array of Urls where footer will be visible..
        	        var base_url=$location.absUrl().split('/').pop();
        	        var base_urls=["login","usersignup","forgotpassword","admin"];
        	       // console.log(template_url.indexOf(base_url));
        	      if ($window.localStorage.access_token) {
        	      //HttpBearerAuth
        	      // make following pages visible on login 
        	     _CURR_PARENT_MENU = $location.path().toString().substring(1);
        	      
        	          config.headers.Authorization = 'Bearer ' + $window.localStorage.access_token;
        	      }
        	     else if (Urls.indexOf(cur_Url) != -1 || base_urls.indexOf(base_url) != -1 )
        	     {
        	       return config;
        	     }
        	     
        	     else
        	     {
        	      
        	      $location.path('/').replace();
        	     }
        	           
        	      return config;
        	       
        	      },
        	      responseError: function (rejection) {
        	      if (rejection.status === 401) {
        	      $location.path('/login').replace();
        	      }
        	      return $q.reject(rejection);
        	      }

         		};
      }]);
	
	
//********************************* Autoresponder funcctionality controller *******************************//	
app.controller('mysettingsController', mysettingsController);
	
	function mysettingsController($scope,$rootScope,$auth,clients, $state,$window,$location,$filter,$http) 
	{
		//************** Logout functionality ********************//
		 $scope.logout = function() {
            delete $window.localStorage.access_token;
            $location.path('/login').replace();
        };
        
		console.log($state.current.name);
		 $scope.activeindex='My_Account';
		 $scope.mycheckResponder=function(status)
		 {
			 	if(status==1)
			 	{    
			 		goToMyChangePassword();
        		}
	        	else if(status==2)
	        	{  
	        		 goToMyAutoresponder();
	        	}
	        	else if(status==3)
	        	{  
	        		//$state.go('mysettings.subscription');
	        		getMySubscription();
	        	}
	        	else if(status==0)
        		{   
	        		goTOMyAccount();
        		}
	        	//$scope.setloading=false;	
	         }
		 
		 if($state.is('mysettings.account'))
		 {
			// var absUrl = $location.url();
			 goTOMyAccount();
			// alert(absUrl);
		 }
		 if($state.is('mysettings.changepassword'))
		 {
			 goToMyChangePassword();
		 }
		 if($state.is('mysettings.autoresponder'))
		 {
			 goToMyAutoresponder();	
		 }
		 if($state.is('mysettings.subscription'))
		 {
			
			 getMySubscription();
			// alert(startIndex);
		 }
		 function goToMyChangePassword()
		 {

			 $scope.myAutoResponder=false;
    		 $scope.changePassword=true;
    		 $scope.myaccount=false;
			 $scope.aweber_integration=false;
			 $scope.icontact=false;
			 $scope.get_response=false; 
			 $scope.mysubscription=false; 
			 $scope.activeindex='Change_Password';
			 $location.path('/mysettings/changepassword').replace();
		 }
		 function goTOMyAccount()
		 {
			 $scope.myAutoResponder=false;
    		 $scope.changePassword=false;
    		 $scope.myaccount=true;
			 $scope.aweber_integration=false;
			 $scope.icontact=false;
			 $scope.get_response=false; 
			 $scope.mysubscription=false; 
			 $scope.activeindex='My_Account';
			 $location.path('/mysettings/account').replace();
			 getCurrentUserDetail();
		 }
		 function goToMyAutoresponder()
		 {
			 	var aweber='aweber';
		        var getresponse='getresponse';
		        var icontact='icontact';
		        getAutoResponderList(aweber);
		 		getAutoResponderList(getresponse);
		 		getAutoResponderList(icontact);
		 		
		 		
			 $scope.myAutoResponder=true;
			 $scope.changePassword=false;
			 $scope.myaccount=false;
			 $scope.aweber_integration=false;
			 $scope.icontact=false;
			 $scope.get_response=false; 
			 $scope.mysubscription=false; 
			 $scope.activeindex='Integration';
			 $location.path('/mysettings/autoresponder').replace();
		 }
		 function getMySubscription()
		 {
			 $scope.myAutoResponder=false;
    		 $scope.changePassword=false;
    		 $scope.myaccount=false;
			 $scope.aweber_integration=false;
			 $scope.icontact=false;
			 $scope.get_response=false; 
			 $scope.mysubscription=true; 
			 $scope.activeindex='subscription';
			 $location.path('/mysettings/subscription').replace();
			 getAllPlansDetails();
			 getCurrentUserDetail();
		 }
		 	var startIndex =  $location.search()['token'];;
			if(angular.isDefined(startIndex) && startIndex !='')
			{
				//alert(startIndex);
				create_recurring_plan(startIndex);
			}
		function create_recurring_plan(startIndex)
		{
			 var Url="payment/getDetailAndCreateRecurring";
			 var data={token:$window.localStorage.access_token,Plan_token:startIndex};
			  var plans_data = clients.getAweberData(data, Url).then(function(response) {
				  if(response.data.status=='success')
				  {
					  	$scope.errormessage=response.data.data.message;
	    				 generate('success',response.data.data.message);
	    				 setTimeout(hideSuccessMessage ,3000);
	    				 $location.path('/mysettings/subscription').replace();
				  }
				  else
				  {
						//$scope.errormessage=response.data.data.message;
	    				// generate('error',response.data.data.message);
	    				 //setTimeout(hideSuccessMessage ,3000);
	    				 $location.path('/mysettings/subscription').replace();
				  }
			  });
			
		}
		 $scope.showResponder=function(data){
			 if(data == 'aweber_integration')
     		{ 
			 $scope.aweber_integration=true;
			 $scope.icontact=false;
			 $scope.get_response=false;
    		 $scope.changePassword=false;
    		 $scope.myaccount=false;
    		 $scope.myAutoResponder=false;
     		}
			else if(data == 'icontact')
	     	{
			   $scope.aweber_integration=false;
				$scope.icontact=true;
			    $scope.get_response=false;
		    	$scope.changePassword=false;
		    	$scope.myaccount=false;
		    	$scope.myAutoResponder=false;
	     	}
			else if(data == 'get_response')
	     	{
					 $scope.aweber_integration=false;
					 $scope.icontact=false;
					 $scope.get_response=true;
		    		 $scope.changePassword=false;
		    		 $scope.myaccount=false;
		    		 $scope.myAutoResponder=false;
	     	}
		 }
		 function getAllPlansDetails()
		 {
			 var Url="payment/getUserAllPlan";
			 var data={token:$window.localStorage.access_token};
			  var plans_data = clients.getAweberData(data, Url).then(function(response) {
					//console.log(response);
					if (response.data.status == 'success') 
					{
						//console.log(response.data.data.currentPlan);
						$scope.currentPlanDetails=response.data.data.currentPlan;
						$scope.AllUserPlan=response.data.data.AllPlans;
					
					}
					else
					{
						$scope.currentPlanDetails=[];
						$scope.AllUserPlan=[];
					
						 $scope.errormessage=response.data.data.message;
	    				 generate('error',response.data.data.message);
	    				 setTimeout(hideSuccessMessage ,3000);
	    				// $location.path('/login').replace();
					}
			  });
		 }
		 
		   function getAutoResponderList(apiname) 
		   {
			   //$scope.loading=true;
			   var Url='api/getApiUserList';
			   var apiname=apiname;
			   var data={api_name : apiname,token: $window.localStorage.access_token};
			   var aweber_data = clients.getAweberUserList(data, Url).then(function(response) {
					//console.log(response);

					if (response.data.status == 'success') 
					{
						var jsonObj = JSON.parse(response.data.data.api_list);
						if(apiname=='aweber')
						{
							if(jsonObj !='')
							{ 
								$scope.switchStatus=true;
							}
							$scope.apiList=jsonObj;
						}
						else  if(apiname=='getresponse')
						{
							if(jsonObj !='')
							{ 
								$scope.switchStatusGetresponse=true;
							}
							$scope.apigetresponseList=jsonObj;
						}
						else if(apiname=='icontact')
						{
							if(jsonObj !='')
							{ 
								$scope.switchStatusIcontact=true;
							}
							            
						}
							
						
					}
			  });
			 
      }
      
     
         function getCurrentUserDetail(){
        	 var userDetail;
 			var data={token: $window.localStorage.access_token};
     		var Url="api/getUserDetail";
     		var updated_password = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
     			//console.log(successCallback.data.data.user.subdomain);
     			//console.log(successCallback.data.data.user.email);
     			if(successCallback.data.status=="success")
 				{	
     				//userDetail = successCallback.data.data.user;
     				$scope.userdetail = successCallback.data.data.user;
     				angular.forEach(successCallback.data.data.user,function(val,key){
     					 $scope[key]=val;
     				});
     				if(angular.isDefined(successCallback.data.data.user.subdomain) && successCallback.data.data.user.subdomain !=''){
     				$window.localStorage.subdomain=successCallback.data.data.user.subdomain;
     				}
     				else
     					{
     					$window.localStorage.subdomain='';
     					}
     				//console.log(successCallback.data.data.user.subdomain);
     				$scope.phone=parseInt(successCallback.data.data.user.contact_no);
     				$scope.setloading=false;			
 				}
     			else
 				{
     				 $scope.errormessage=successCallback.data.data.message;
    				 generate('error',successCallback.data.data.message);
    				 setTimeout(hideSuccessMessage ,3000);
    				// $state.go('userauth');
    				 $location.path('/login').replace();

 				}
     			
     		});
          }
         

         
        $scope.checkDomain= function(subdomain){
        	 //alert(subdomain);
        	if(angular.isDefined(subdomain) && subdomain !=''){
        		//alert(subdomain);
        		var data={token: $window.localStorage.access_token,sub_domain:subdomain};
         		var Url="api/checkYourDomain";
         		var updated_domain = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
         			//console.log(successCallback);
         			if(successCallback.data.status=="success")
     				{
         				//$scope.subdomainerror=successCallback.data.data.message;
         				$scope.frmdomainsetting.subdomain.$setValidity("unique", false); 
         				//$scope.frmdomainsetting.subdomain.$setValidity("frmdomainsetting.subdomain.$error.unique", false);
         				 //ctrl.$setValidity('unique', true);
         			}
         			else if(successCallback.data.status=="successwithnoerro")
     				{
         				$scope.frmdomainsetting.subdomain.$setValidity("unique", true); 
     				}
         			else
     				{
         				 $scope.errormessage=successCallback.data.data.message;
        				 generate('error',successCallback.data.data.message);
        				 setTimeout(hideSuccessMessage ,3000);
        				 $location.path('/login').replace();
 
     				}
         			
         		});
        	}
        	      	
        	
         }
       
        $scope.checkEmail= function(email){
       	 //alert(subdomain);
       	if(angular.isDefined(email) && email !=''){
       		//alert(subdomain);
       		var data={token: $window.localStorage.access_token,email_id:email};
        		var Url="api/checkYourEmail";
        		var updated_domain = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
        			//console.log(successCallback);
        			if(successCallback.data.status=="success")
    				{
        				$scope.emailerror=successCallback.data.data.message;
        				$scope.frmdomainsetting.email.$setValidity("unique", false); 
        				//$scope.duplicate=true;
        			}
        			else if(successCallback.data.status=="successwithnoerro")
     				{
         				$scope.frmdomainsetting.email.$setValidity("unique", true); 
     				}
        			else
    				{
        				 $scope.errormessage=successCallback.data.data.message;
        				 generate('error',successCallback.data.data.message);
        				 setTimeout(hideSuccessMessage ,3000);
        				// $state.go('userauth');
        				 $location.path('/login').replace();
    				}
        			
        		});
       	}
       	      	
       	
        }
        $scope.personalformSubmit=function(){
        	$scope.submitted=true;
        	 if($scope.frmdomainsetting.$invalid)
			      return false;
        	//console.log($scope.subdomain); 
            var sub_domain=$scope.subdomain;
        	var email=$scope.email;
        	var firstname=$scope.first_name;
        	var lastname=$scope.last_name;
        	var contact=$scope.contact_no;
        	var data={token: $window.localStorage.access_token,email_id:email,subdomain:sub_domain,first_name:firstname,last_name:lastname,contact_number:contact};
    		var Url="api/updateProfileInfo";
    		var updated_domain = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
    			//console.log(successCallback);
    			if(successCallback.data.status=="success")
				{
    				$scope.success_message=successCallback.data.data.message;
   				 	generate('success',successCallback.data.data.message);
   				 	setTimeout(hideSuccessMessage ,3000);
    			}
    			else
				{
    				 $scope.errormessage=successCallback.data.data.message;
    				 generate('error',successCallback.data.data.message);
    				 setTimeout(hideSuccessMessage ,3000);
    				 $location.path('/login').replace();
				}
    			
    		});
        }
	       //************** Password Change functionality ********************//
                $scope.old_password='';
         		$scope.new_password='';
		    	$scope.confirm_password='';
		        $scope.updatePassword = function()
		         { console.log("pass");  
		        	$("#emailloader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/loader-img.gif"></div>');
		        	var old_password = $scope.old_password;
		        	var password = $scope.new_password;
		        	var confirm_password = $scope.confirm_password;
		        	if(password != confirm_password)
		        	{
		 				generate('error',"Password Does not Match");
						setTimeout(hideSuccessMessage ,3000);
		        		$scope.pass_error_message="Password Does not Match";
		        		return false;
		        	}
		        	else
		        	{   	
		        	var data={pass: password,old_pass: old_password,token: $window.localStorage.access_token};
		    		var Url="api/updatePassword";
		    		var updated_password = clients.getTemplateData(data, Url).then(function(response){
		    			if (response.data.status == 'success') {
		    				$("#emailloader").html('');
		    				$scope.pass_success_message="Your Password is successfully changed";
		    				generate('success',response.data.data.Message);
		    				setTimeout(hideSuccessMessage ,3000);
		    				$scope.old_password='';
		    	        	$scope.new_password='';
		    	        	$scope.confirm_password='';
		    	        	$scope.pass_error_message='';
		    			}
		    			else 
		     			{
		    				$("#emailloader").html('');
		     				generate('error',response.data.data.Message);
		    				setTimeout(hideSuccessMessage ,3000);
		    				$scope.pass_error_message= response.data.data.Message;
		    				pass_success_message='';
		    				$scope.old_password='';
		    	        	$scope.new_password='';
		    	        	$scope.confirm_password='';
		    	        	$scope.pass_success_message='';
		     			}
		    		});
		        	}
		         }
		  
		
		
		//**************** Get query string ***************************//
     
		
		 var startIndex = $location.absUrl().indexOf('oauth_token');
         var endIndex = $location.absUrl().indexOf('&display'); 
         var stringLength = (endIndex - startIndex); 
         if (stringLength > 0) {
             var substring = $location.absUrl().substr(startIndex, stringLength);
             if (substring.length > 0) {
                 var arrParam = substring.split('&');
                 var outhToken=arrParam[0].split('=')[1];
                 var outhverifier=arrParam[1].split('=')[1];
                // console.log(outhToken);console.log(outhverifier);
                 //$scope.callbackrequest=function(){
                	  var Url='api/getAccessToken';
                	  var apiname='aweber';
        			  var data={
        					  oauth_token: outhToken,
        					  oauth_verifier: outhverifier,
        					  api_name : apiname,
        					  token:$window.localStorage.access_token
        					};
        			  var aweber_data = clients.getAweberData(data, Url).then(function(response) {
      					//console.log(response);
      					if (response.data.status == 'Success') 
      					{
      						var msg=response.data.data.message;
							//alert(msg);
      					}
      			  });
               //  }
                // $scope.autoresponder.oauth_verifier = arrParam[1];
             }
         }
		 var callbackobject= $location.absUrl().indexOf('oauth_verifier');
		  //console.log(callbackobject);
		 if(callbackobject > -1)
		 {
			 $scope.changePassword=false;
    		 $scope.myaccount=false;
    		 $scope.myAutoResponder=true;

		 }
		 /* else
		 {
			 $scope.changePassword=false;
    		 $scope.myAutoResponder=false;
			 $scope.myaccount=true;
		 }*/
		
		
		//To go back on integration page
		 $scope.backResponder=function(data){
			 if(data=='aweber_integration')
     		{
			 $scope.aweber_integration=false;
			 $scope.icontact=false;
			 $scope.get_response=false;
    		 $scope.changePassword=false;
    		 $scope.myaccount=false;
    		 $scope.myAutoResponder=true;
     		}
			 else if(data=='icontact')
     		{
				 $scope.aweber_integration=false;
				 $scope.icontact=false;
				 $scope.get_response=false;
	    		 $scope.changePassword=false;
	    		 $scope.myaccount=false;
	    		 $scope.myAutoResponder=true;
     		}
			 else if(data=='get_response')
     		{
				 $scope.aweber_integration=false;
				 $scope.icontact=false;
				 $scope.get_response=false;
	    		 $scope.changePassword=false;
	    		 $scope.myaccount=false;
	    		 $scope.myAutoResponder=true;
     		}
		 }
		
		 
				 
		  $scope.getmyaweber=function(consumerkey,consumersecret){ 
			  $("#awebloader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/loader-img.gif"></div>');
			  $('#aweber_form').parsley();
			  $("#awebserbtn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Updating...');

         

			  var Url='api/aweberRequest';
			  var api_name='aweber';
			  //alert($scope.consumerKey); 
			// alert($scope.consumerSecret);
			  var data={
					 // consumerKey: $scope.consumerKey,
					 // consumerSecret: $scope.consumerSecret,
					  apiName:api_name,
					  token:$window.localStorage.access_token,
					};
			 // alert(data);
			  var aweber_data = clients.getAweberData(data, Url).then(function(response) {
					//console.log(response);
					if (response.data.status == 'Success') {
						var url=response.data.data.redirectUrl;
						$window.location.href = url;
						  $("#awebserbtn").html('Update');
					}
					else if (response.data.status == 'Fail') {
						 $("#awebserbtn").html('Update');
							var msg=response.data.data.message;
							alert(msg);
						}
			  });
	}
		
		 $scope.getmygetresponse=function(apikey){
			  $("#getresponsebtn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Updating...');
			  $('#getresponse_form').parsley();
			  var Url='api/getResponseRequest';
			  var api_name='getresponse';
			  $scope.getresponseApiKey=apikey;
			 // alert(apikey);
			  var data={
					  apiKey: $scope.getresponseApiKey,
					  apiName: api_name,
					  token:$window.localStorage.access_token
					};
			  var aweber_data = clients.getAweberData(data, Url).then(function(response) {
					//console.log(response);
					if (response.data.status == 'Fail') {
						  $("#getresponsebtn").html('Update');
						var msg=response.data.data.message;
						  generate('error',msg);
		     				 setTimeout(hideSuccessMessage ,3000);
					}
					else
					{
						  $("#getresponsebtn").html('Update');
						$scope.myAutoResponder=true;
						$scope.get_response=false;
						$scope.switchStatusGetresponse=true;
						var msg=response.data.data.message;
						 generate('success',msg);
	     				 setTimeout(hideSuccessMessage ,3000);
					}
					
			  });
		  }
		 $scope.getmyicontact=function(apiUserName,apiPassword){
			  $("#icontactbtn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Updating...');
			  $('#icontact_form').parsley();
			  var Url='api/getIcontactRequest';
			  $scope.apiUserName=apiUserName;
			  $scope.apiPassword=apiPassword;
			  var api_name='icontact';
			 // var api_id="4eduYYGDhsIE29ymOhtB8w2QUZDNvbxl";
			  var data={
					  //api_id: api_id,
					  api_user_name: $scope.apiUserName,
					  api_pwd: $scope.apiPassword,
					  apiName: api_name,
					  token:$window.localStorage.access_token
					};
			  var aweber_data = clients.getAweberData(data, Url).then(function(response) {
				 // console.log(response);
				  if (response.data.status == 'Fail') {
					  $("#icontactbtn").html('Update');
						var msg=response.data.data.message;
						 generate('error',msg);
	     				 setTimeout(hideSuccessMessage ,3000);
					}
				  else
				  {
					  $("#icontactbtn").html('Update');
					  $scope.switchStatusIcontact=true;
					  $scope.myAutoResponder=true;
					  $scope.icontact=false;
				  }
			  });
		  }
		 //***************************** change status of autoresponder ***********************//
		 $scope.changeAweberStatus=function(apiname)
		 {
			 if($scope.switchStatus ==true){var status=1;}else{var status=0;}
				changeStatus(apiname,status);
		}
		 $scope.changeGetresponseStatus=function(apiname){
			 if($scope.switchStatusGetresponse ==true){var status=1;}else{var status=0;}
				changeStatus(apiname,status);
		 }
		 $scope.changeIcontractStatus=function(apiname){
			 if($scope.switchStatusIcontact ==true){var status=1;}else{var status=0;}
				changeStatus(apiname,status);
		 }
		 function changeStatus(apiname,status)
		 {
		
			 var Url='api/changeAutoresponderStatus';
			 var data={
					  api_name: apiname,
					  status: status,
					  token:$window.localStorage.access_token
					};
			  var aweber_data = clients.getAweberData(data, Url).then(function(response) {
					//console.log(response);
					if (response.data.status == 'success') {
						//$('.activeclass').addclass('active');
						getAutoResponderList(apiname);
						
					}
			  });
		  }
		 
      //******************************Check Disconnect status of integration*****************************//
		     $scope.responderDisconnectStatus=function(apiname) {
				//alert(apiname);
			 // var Url='api/disconnectStatus';
			  var apiname=apiname;
			 // var data={api_name : apiname};
			 // var aweber_data = clients.getAweberUserList(data, Url).then(function(response) {
					//console.log(response);				 
					//if (response.data.status == 'success') {
				 
						if(apiname == 'aweber')
						{   changeStatus(apiname,0);
							 $scope.aweber_integration=true;
							 $scope.icontact=false;
							 $scope.get_response=false;
				    		 $scope.changePassword=false;
				    		 $scope.myaccount=false;
				    		 $scope.myAutoResponder=false;
				    		 $scope.switchStatus=false;
				    		 $scope.apigetresponseList=false;
				    		 $scope.apiList=false;
						}
						else if(apiname == 'icontact')
						{	changeStatus(apiname,0);
						 	$scope.icontact=true;
							 $scope.aweber_integration=false;
							 $scope.switchStatusIcontact=false;
							 $scope.apiIcontactList=false;
							 $scope.get_response=false;
				    		 $scope.changePassword=false;
				    		 $scope.myaccount=false;
				    		 $scope.myAutoResponder=false;
						}
						else if(apiname == 'getresponse')
						{   
							changeStatus(apiname,0);
							$scope.switchStatusGetresponse=false;
							$scope.apigetresponseList=false;
							 $scope.aweber_integration=false;
							 $scope.icontact=false;
							 $scope.get_response=true;
				    		 $scope.changePassword=false;
				    		 $scope.myaccount=false;
				    		 $scope.myAutoResponder=false;
						}
						
					//}
			 // }); 
		 }
		     //*************** Payment Cancel Request **************************//
		     
		     $scope.paymentOption=function(paymentoption,user_plan_id)
		     { 
		    	 var elementrefundform = angular.element('#refundpaymentform');
		    	 var elementcancelform = angular.element('#cancelpaymentform');
		    	 var elementchangepaymentform = angular.element('#updatepaymentform');
		    	 $rootScope.user_plan_id=user_plan_id;
		    	// $rootScope.first_name=$scope.first_name;
		    	 //$rootScope.email=$scope.email;
		    	 if(paymentoption=='cancel')
	    		 {
		    			var answer = confirm("You can't reactivate your profile again. Do you want to cancel?")
						if (answer) 
						{
							elementcancelform.modal('show');
							$rootScope.payment_type='1';
							
						}
	    		 }
		    	 else if(paymentoption=='refund')
	    		 {
		    		 var answer = confirm("Are you sure you want to refund?")
						if (answer) 
						{
							
							
							elementrefundform.modal('show');
							$rootScope.payment_type='2';
						}
	    		 }
		    	 else if(paymentoption=='changepayment')
	    		 {
		    		 var answer = confirm("Are you sure you want to change payment plan?")
						if (answer) 
						{
							var url="payment/getpaymentplans";
							var data={};
							var template_data = clients.CategoryDetailData(data, url).then(function(response) 
								{
									$rootScope.paymentplans=response.data.data.plans;
								});
							elementchangepaymentform.modal('show');
							$rootScope.payment_type='3';
						}
	    		 }
		    	 
		    	 
		     }
		     //*********************** Cancel Subscription *************************//
		     $scope.saveUserCancelRequest = function(){
		    	
		    	 var elementcancelform = angular.element('#cancelpaymentform');
		    	  if($scope.paymentcancel=='' || !angular.isDefined($scope.paymentcancel) ){
	  		   		$scope.errormessage="You must enter the Reason for cancellation.";
	 					generate('error',$scope.errormessage);
						setTimeout(hideSuccessMessage ,3000);
						return false;
					}
		    	
		    	 var url="payment/cancelThePaymentProgrammatically";
		    	  $("#cancel-btn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Saving...');
		    	
		    	 var data={reason:$scope.paymentcancel,
		    			 user_plan_id: $scope.user_plan_id,
		    			 payment_type:$scope.payment_type,
		    			 token:$window.localStorage.access_token,
		    			 user_name:$scope.user_name,
		    			 user_email:$scope.email};		  
					var template_data = clients.CategoryDetailData(data, url).then(function(response) 
					{
						if (response.data.status == 'success') {
							$scope.pass_success_message=response.data.data.message;
							generate('success',$scope.pass_success_message);
							setTimeout(hideSuccessMessage ,3000);
							$("#cancel-btn").html('Save');
							$('#cancelpaymentform').hide();
							$(".modal-backdrop").fadeOut();
	 			        	$("body").removeClass('modal-open');
						}
						else
						{
							$scope.errormessage=response.data.data.message;
							generate('error',$scope.errormessage);
							setTimeout(hideSuccessMessage ,3000);
							$("#cancel-btn").html('Save');
							$('#cancelpaymentform').hide();
							$(".modal-backdrop").fadeOut();
	 			        	$("body").removeClass('modal-open');
							
						}
					});
		     }
		     
		     //*********************** RREfund REquest **************************//
		     $scope.saveRefundUserRequest=function(){
		    	 var elementrefundform = angular.element('#refundpaymentform');
		    	  if($scope.paymentrefund=='' || !angular.isDefined($scope.paymentrefund) ){
		  		   		$scope.errormessage="You must enter the reason for refund.";
		 					generate('error',$scope.errormessage);
							setTimeout(hideSuccessMessage ,3000);
							return false;
						}
		    	  $("#refund-btn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Saving...');
			    	 var url="payment/paymentDeclineRequest";
			    	 var data={reason:$scope.paymentrefund,
			    			 user_plan_id: $scope.user_plan_id,
			    			 payment_type:$scope.payment_type,
			    			 user_name:$scope.user_name,
			    			 user_email:$scope.email,
			    			 token:$window.localStorage.access_token};		  
						var template_data = clients.CategoryDetailData(data, url).then(function(response) 
						{
							if (response.data.status == 'success') {
								$scope.pass_success_message=response.data.data.message;
								generate('success',$scope.pass_success_message);
								setTimeout(hideSuccessMessage ,3000);
								$("#refundpaymentform").hide();
								$("#refund-btn").html('Save');
								$(".modal-backdrop").fadeOut();
		 			        	$("body").removeClass('modal-open');
							}
							else
							{
								$scope.errormessage=response.data.data.message;
								generate('error',$scope.errormessage);
								setTimeout(hideSuccessMessage ,3000);
								$("#refund-btn").html('Save');
								$('#cancelpaymentform').hide();
								$(".modal-backdrop").fadeOut();
		 			        	$("body").removeClass('modal-open');
							}
						});
		     }
		     
		   $scope.changeUserPaymentPlan=function(){
			  // var elementrefundform = angular.element('#refundpaymentform');
		    	  if($scope.payment_plan=='' || !angular.isDefined($scope.payment_plan) ){
		  		   		$scope.errormessage="Please select payment plan.";
		 					generate('error',$scope.errormessage);
							setTimeout(hideSuccessMessage ,3000);
							return false;
						}
		    	  $("#changePlan-btn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Saving...');
			    
			   
			   
			   var url="payment/changeUserPaymentPlan";
			    var data={ user_plan_id: $scope.user_plan_id, 
					   plan_id: $scope.payment_plan,
		    			 payment_type:$scope.payment_type,
		    			 user_name:$scope.user_name,
		    			 user_email:$scope.email,
		    			 token:$window.localStorage.access_token};	
				var template_data = clients.CategoryDetailData(data, url).then(function(response) 
						{
							if(response.data.status=='success')
							{
								var token=response.data.data.token;
								var url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='+token;
								$window.location.href = url;
							}
						
							
						});
		   }
		  
		   
		 
		  
	}
	//****************************** Tabs for categories************************************//
	app.controller('categorycontroller', categorycontroller);
	
	function categorycontroller($scope,$auth,clients,$sce, $state,$window,$location,$rootScope,$http) 
	{
		 
		  
		if(!angular.isDefined($window.localStorage.access_token))
		{
			 $location.path('/login').replace();
		}
		var Url='admin/categoryData'; 
		var catid='0';
		var step_id=$window.localStorage.step_id;
		  var data={category_id: catid};		  
			var template_data = clients.CategoryDetailData(data, Url).then(function(response) 
			{
				if (response.data.status == 'success') {
					$scope.catArr = response.data.data.catArr;
				}
			});
			
			$scope.checkCategory=function(status)
			{
				var Url='admin/categoryData'; 
				  var data={category_id: status};
					var template_data = clients.CategoryDetailData(data, Url).then(function(response) 
					{
						if (response.data.status == 'success') {
							$scope.catArr = response.data.data.catArr;
						}
					});
				
			}
		
			
	}
	
//**********************Super Admin*************************//
	app.controller('AdminController', AdminController);

	 function AdminController($scope,$rootScope,$auth,clients, $state,$window,$location,$filter,$sce) {

		 console.log($state.current.name);
     //**********Admin login**************//
		$('#adminlogin_form').parsley();
		$scope.adminlogin=function()
		{  
			$("#adminlogin_btn").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
			$("#adminlogin_btn").attr('disabled', 'disabled');
			var credentials = {email: $scope.adminemail, pass: $scope.adminpassword}
			var Url = 'superadmin/authenticateAdmin';
			var admin_token = clients.getTemplateData(credentials, Url).then(function(response){ 
				//console.log(response);
				if (response.data.status == 'success') 
				{
					$("#adminlogin_btn").html('Login');
					$("#adminlogin_btn").removeAttr('disabled');
					$window.localStorage.access_token = response.data.data.token;
					 $location.path('/admindashboard/home').replace();
				}
				else
				{
					$("#adminlogin_btn").html('Login');
					$("#adminlogin_btn").removeAttr('disabled');
					$scope.error=response.data.data.message;
					 generate('error',response.data.data.message);
     				 setTimeout(hideSuccessMessage ,3000);
				}
			});
		}
		
		//********Redirect on click*********//
		function goToUserList()
		{
			$scope.dashboard=false;
	   		 $scope.userlist=true;
	   		 $scope.userstatus=false;
	   		 $scope.adduser=false;
	   		 $scope.edituser=false;
	   		 $scope.paymentstatus=false;
	   		 $scope.addnewpaymentplans=false;
	   		 $scope.editPaymentPlans=false;
	   		 $scope.userBillinghostory=false;
	   		 $scope.myprofile=false;
	   		$scope.cancellationlist=false;
	   		$scope.refundlist=false;
			 $scope.activeindex='userlist';
			 $location.path('/admindashboard/users').replace();
			 userList();
		}
		function goToAdminDashboard()
		{
			 $scope.dashboard=true;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
			 $scope.activeindex='admindashboard';
			 $location.path('/admindashboard/home').replace();
		}
		function goToPaymentList()
		{
			 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=true;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
			 $scope.activeindex='payment';
			 $location.path('/admindashboard/payment').replace();
			 paymentPlansList();
		}
		function goToAddNewUser()
		{
			$scope.errormessage='';
    	 	$scope.success_message='';
    	 	$scope.first_name='';
    	 	$scope.last_name='';
    	 	$scope.email='';
    	 	$scope.password='';
    	 	$scope.confirm_paswd='';
    	 	$scope.phone_no='';
    	 	
    		 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=true;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
    		 $scope.activeindex='userlist';
			 $location.path('/admindashboard/users/new').replace();
		}
		function goToAddNewPaymentPlan()
		{
			$scope.errormessage='';
		 	$scope.success_message='';
		 	$scope.name='';
				$scope.price='';
				$scope.validity='';
				$scope.status='';
				
    		 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=true;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
    		 $scope.activeindex='payment';
			 $location.path('/admindashboard/payment/new').replace();
		}
		function goToAdminChnagepassword()
		{
			$scope.errormessage='';
		 	$scope.success_message='';
		 	
    		 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=true;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
    		 $scope.activeindex='pwd_change';
    		 
			 $location.path('/admindashboard/changepassword').replace();
		}
		function goToUpdateMyProfile()
		{
			$scope.errormessage='';
		 	$scope.success_message='';
    		$scope.myprofile=true;
    		 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
    		 $scope.activeindex='my_profile';
    		 getCurrentUserDetail();
			 $location.path('/admindashboard/myprofile').replace();
		}
		function goToUpdateUSerProfile()
		{
			 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=true;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
			 $scope.activeindex='userlist';
			 editUserData();
		}
		function gotToUpdatePaymentPlan()
		{
			 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=true;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
    		 $scope.activeindex='payment';
			 updatePaymentPlan();
		}
		function goToUSerPaymentHistory()
		{
			 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=true;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=false;
    		 $scope.activeindex='userlist';
    		 getUserBillingHistory();
		}
		function goToCancelRequests()
		{
			 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=true;
    		 $scope.refundlist=false;
    		 $scope.activeindex='cancellation';
    		 getAllCancellationList();
    		 $location.path('/admindashboard/cancellationlist').replace();
		}
		function goToRefundRequests()
		{
			 $scope.dashboard=false;
    		 $scope.userlist=false;
    		 $scope.userstatus=false;
    		 $scope.adduser=false;
    		 $scope.edituser=false;
    		 $scope.paymentstatus=false;
    		 $scope.addnewpaymentplans=false;
    		 $scope.editPaymentPlans=false;
    		 $scope.userBillinghostory=false;
    		 $scope.myprofile=false;
    		 $scope.cancellationlist=false;
    		 $scope.refundlist=true;
    		 $scope.activeindex='refund';
    		 getAllRefundList();
    		 $location.path('/admindashboard/refundlist').replace();
		}
		$scope.checkUser=function(status)
		{ 
        	if(status==1)
    		{    
        		goToUserList();
    		}
        	if(status==2)
    		{    
        		goToAdminDashboard();
    		}
        	
        	else if(status==3)
    		{   
        		goToPaymentList();
    		}
        	else if(status==4)
    		{   
        		goToAddNewUser();
    		}
        	else if(status==5)
    		{
        		goToAddNewPaymentPlan();
        	}
        	else if(status==6)
    		{
        		goToAdminChnagepassword();
    		}
        	else if(status==7)
    		{
        		goToUpdateMyProfile();
    		}
        	else if(status==8)
    		{
        		goToCancelRequests();
    		}
        	else if(status==9)
    		{
        		goToRefundRequests();
    		}
        	
		}
		  
	    if($state.is('admindashboard.users'))
		 {
	    	goToUserList();
		 }
	    if($state.is('admindashboard.payment'))
		 {
	    	goToPaymentList();
	    	
		 }
		 if($state.is('admindashboard.users-id'))
		 {
			 goToUpdateUSerProfile();
		 }
		 if($state.is('admindashboard.payment-id'))
		 {
			 gotToUpdatePaymentPlan();
		 }
		 if($state.is('admindashboard.paymenthistory'))
		 {
			 goToUSerPaymentHistory();
		 }
		 if($state.is('admindashboard.users-new'))
		 {
			 goToAddNewUser();
		 }
		 if($state.is('admindashboard.payment-new'))
		 {
			 goToAddNewPaymentPlan();
		 }
		 if($state.is('admindashboard.home'))
		 {
			 goToAdminDashboard();
		 }
		 if($state.is('admindashboard.changepassword'))
		 {
			 goToAdminChnagepassword();
		 }
		 if($state.is('admindashboard.myprofile'))
		 {
			 goToUpdateMyProfile();
			 
		 }
		 if($state.is('admindashboard.cancellationlist'))
		 {
			 goToCancelRequests();
			 
		 }
		 if($state.is('admindashboard.refundlist'))
		 {
			 goToRefundRequests();
			 
		 }
		$scope.chkeditUser=function(id)
		{ 
			
			 var url="/admindashboard/users/"+id;
			 $location.path(url).replace();
			 goToUpdateUSerProfile();
		}
		$scope.editPaymentPlan=function(id)
		{ 
			
			 var url="/admindashboard/payment/"+id;
			 $location.path(url).replace();
			 gotToUpdatePaymentPlan();
		}
		 $scope.userBillingHistory=function(billUserId)
        {
        		
			 var paymentUrl='/admindashboard/paymenthistory/'+billUserId;
			 $location.path(paymentUrl).replace();
			 goToUSerPaymentHistory();
        	
        }
		 function getAllRefundList()
		 {
			 	var data = {token: $window.localStorage.access_token};
	        	var Url = 'payment/getAllRefundList';
				var payment_plan = clients.getTemplateData(data,Url).then(function(successCallback){ 
					//console.log(successCallback);
					if(successCallback.data.status=='success')
					{ 
						$scope.refunds = successCallback.data.data.refunds;
						
	     				
					}
					else
					{ 
	     				 $scope.errormessage=successCallback.data.data.message;
	    				 generate('error',successCallback.data.data.message);
	    				 setTimeout(hideSuccessMessage ,3000);
					}
					
	         });
		 }
		 function getAllCancellationList()
		 {
			 var data = {token: $window.localStorage.access_token};
	        	var Url = 'payment/getAllCancellationList';
				var payment_plan = clients.getTemplateData(data,Url).then(function(successCallback){ 
					//console.log(successCallback);
					if(successCallback.data.status=='success')
					{ 
						$scope.cancelList = successCallback.data.data.cancelUser;
						
	     				
					}
					else
					{ 
	     				 $scope.errormessage=successCallback.data.data.message;
	    				 generate('error',successCallback.data.data.message);
	    				 setTimeout(hideSuccessMessage ,3000);
					}
					
	         });
		 }
	    function updatePaymentPlan()
        { 
       	 		$scope.errormessage='';
			 	$scope.success_message='';
			 	$scope.name='';
  				$scope.price='';
  				$scope.validity='';
  				$scope.status='';
	        	 var planId = $location.absUrl().split('/').pop();
	        	 //console.log(userId);
	        	var data = {plan_id:planId};
	        	var Url = 'payment/getPlanDetail';
				var payment_plan = clients.getTemplateData(data,Url).then(function(successCallback){ 
					//console.log(successCallback);
					if(successCallback.data.status=='success')
					{ 
						$scope.plandetail = successCallback.data.data.plan;
						angular.forEach(successCallback.data.data.plan,function(val,key){
	     					 $scope[key]=val;
	     				});
	     				
					}
					else
					{ 
	     				 $scope.errormessage=successCallback.data.data.message;
	    				 generate('error',successCallback.data.data.message);
	    				 setTimeout(hideSuccessMessage ,3000);
					}
					$scope.setloading=false;
	         });
        }
        
        
        
      
		
		 
		 //************User list**************//
		 function userList()
		 {      
				var Url = 'api/alluser';
				var admin_token = clients.getUsersData(Url).then(function(response){ 
					//console.log(response);
					if(response.data.status=='success')
					{
						$scope.user = response.data.data.users;
					}
					else
					{
						 generate('error',"An error occurred");
	     				 setTimeout(hideSuccessMessage ,3000);
					}
					$scope.setloading=false;
		     	});
		 }
		 
		 //************Plans list**************//
		 function paymentPlansList()
		 {      
			 	var Url = 'payment/getpaymentplans'
				var admin_token = clients.getUsersData(Url).then(function(response){ 
					//console.log(response);
					if(response.data.status=='success')
					{
						$scope.planArr=response.data.data.plans;
						//$scope.user = response.data.data.users;
					}
					else
					{
						 generate('error',"An error occurred");
	     				 setTimeout(hideSuccessMessage ,3000);
					}
					$scope.setloading=false;
		     	});
		 }
		 
	//************Add New User************//
		   $('#adduser_form').parsley();
	         $scope.userregister=function(){
	        	 $scope.loadingModel = true;
	        	 var credentials = {
	      				first_name: $scope.first_name,
	      				last_name: $scope.last_name,
	      				email: $scope.email,
	      				password: $scope.password,
	      				password_confirmation: $scope.confirm_paswd,
	      				contact_no: $scope.phone_no	      				
	      			}
	        	 if($scope.password != $scope.confirm_paswd)
	  			{ 
	      			generate('error','Password and confirm password should be the same.');
	 					setTimeout(hideSuccessMessage ,3000);
	 					return false;
	  			}	        	 
	        	 var Url="superadmin/userRegister";
	        	 userRegisteration(credentials,Url)
	         }

	         function userRegisteration(credentials,Url)
	         {	        	 
	        	 	var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
	                 //console.log(response);
	     			 if(response.data.status=="success")
	 				 {
	     				 $scope.usermessage=response.data.data.message;
	     				 generate('success',response.data.data.message);
	     				 setTimeout(hideSuccessMessage ,3000);
	 				 }
     				else
	 				 {
	     				 $scope.usererrormessage=response.data.data.message;
	     				 generate('error',response.data.data.message);
	     				 setTimeout(hideSuccessMessage ,3000);
	 				 }	     			 
	     			$scope.loadingModel = false;
	      		 });

	         }
	         
	     //************Edit User*************//
	         var chkurl = $location.absUrl().split('/').slice(-2)[0];
	         function editUserData()
	         { 
	        	 	$scope.errormessage='';
	        	 	$scope.success_message='';
	        	 	$scope.first_name='';
	        	 	$scope.last_name='';
	        	 	$scope.email='';
	        	 	$scope.password='';
	        	 	$scope.confirm_paswd='';
	        	 	$scope.phone_no='';
		        	 var userId = $location.absUrl().split('/').pop();
		        	var data = {user_id:userId};
		        	var Url = 'api/getUserDetail';
					var admin_token = clients.getTemplateData(data,Url).then(function(successCallback){ 
						//console.log(successCallback);
						if(successCallback.data.status=='success')
						{ 
							$scope.userdetail = successCallback.data.data.user;
							//console.log($scope.userdetail);
		     				angular.forEach(successCallback.data.data.user,function(val,key){
		     					 $scope[key]=val;
		     				});
		     				$scope.phone=parseInt(successCallback.data.data.user.contact_no);
						}
						else
						{ 
		     				 $scope.errormessage=successCallback.data.data.message;
		    				 generate('error',successCallback.data.data.message);
		    				 setTimeout(hideSuccessMessage ,3000);
						}
						$scope.setloading=false;
		         });
	         }	
	         
	         $('#edituserdetailform').parsley();
	         $scope.updateUserInfo=function(){
	        	 //$scope.loadingModel = true;
	        	// $scope.submitted=true;
	        	if($scope.frmusersetting.$invalid)
	        	{ 
	        		return false;
	        	}
	        	else
	        	{
	        		 var userId = $location.absUrl().split('/').pop();
		        	 var usertoken=$window.localStorage.access_token;
		        	 var credentials = {
		        			 user_id:userId,
		      				first_name: $scope.first_name,
		      				last_name: $scope.last_name,
		      				email_id: $scope.email,
		      				contact_number: $scope.contact_no,
		      				subdomain: $scope.subdomain,
		      				user_status: $scope.user_status,
		      				token:usertoken
		      			}
		        	 var Url="api/updateUserProfileInfo";
		        	 var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
		                // console.log(response);
		     			 if(response.data.status=="success")
		 				 {
		     				 $scope.success_message=response.data.data.message;
		     				 generate('success',response.data.data.message);
		     				 setTimeout(hideSuccessMessage ,3000);
		     				 $scope.errormessage='';
		     				
		     				
		 				 }
	     				else
		 				 {
		     				 $scope.errormessage=response.data.data.message;
		     				 generate('error',response.data.data.message);
		     				 setTimeout(hideSuccessMessage ,3000);
		     				 $scope.success_message='';
		 				 }	     			 
		     			$scope.loadingModel = false;
		      		 });
	        	}
	        	
	         }
	         
	         
	         
	         
	         
	         
	         
	         
	        
	        
	         $scope.checkUniqueEmail= function(email){
	        	 //alert(subdomain);
	        	if(angular.isDefined(email) && email !=''){
	        		 var userId = $location.absUrl().split('/').pop();
	        		var data={token: $window.localStorage.access_token,email_id:email,user_id:userId};
	         		var Url="api/checkYourEmail";
	         		var updated_domain = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
	         			//console.log(successCallback);
	         			if(successCallback.data.status=="success")
	     				{
	         				$scope.errormessage=successCallback.data.data.message;
	         				$scope.frmusersetting.email.$setValidity("unique", false); 
	         				return false;
	         			}
	         			else if(successCallback.data.status=="successwithnoerro")
	      				{
	          				$scope.frmusersetting.email.$setValidity("unique", true); 
	          				
	      				}
	         			else
	     				{
	         				 $scope.errormessage=successCallback.data.data.message;
	         				 generate('error',successCallback.data.data.message);
	         				 setTimeout(hideSuccessMessage ,3000);
	         				// $state.go('userauth');
	         				// $location.path('/login').replace();
	     				}
	         			
	         		});
	        	}
	        	      	
	        	
	         }
	         
	         
	         
	         $('#addplans_form').parsley();
	         $scope.addNewPaymentPlan=function(){
	        	 $scope.loadingModel = true;
	        	 var credentials = {
	      				plan_name: $scope.name,
	      				plan_price: $scope.price,
	      				plan_validity: $scope.validity,
	      				   				
	      			}
	        	        	 
	        	 var Url="payment/addNewPaymentPlan";
	        		var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
		                 //console.log(response);
		     			 if(response.data.status=="success")
		 				 {
		     				 $scope.usermessage=response.data.data.message;
		     				 generate('success',response.data.data.message);
		     				 setTimeout(hideSuccessMessage ,3000);
		 				 }
	     				else
		 				 {
		     				 $scope.usererrormessage=response.data.data.message;
		     				 generate('error',response.data.data.message);
		     				 setTimeout(hideSuccessMessage ,3000);
		 				 }	     			 
		     			$scope.loadingModel = false;
		      		 });
	         }
	         
	        /********************************** Edit Plans Details ****************************/ 
	         $('#editpaymentplansform').parsley();
	         $scope.updatePaymentPlan = function(){
	        	 //$scope.loadingModel = true;
	        	// $scope.submitted=true;
	        	if($scope.editpaymentplan.$invalid)
	        	{ 
	        		return false;
	        	}
	        	else
	        	{
	        		 var planId = $location.absUrl().split('/').pop();
		        	 var usertoken=$window.localStorage.access_token;
		        	 var credentials = {
		        			 	plan_id:planId,
		        			 	plan_name: $scope.name,
			      				plan_price: $scope.price,
			      				plan_validity: $scope.validity,
			      				plan_status: $scope.status,
		      			}
		        	 var Url="payment/updatePaymentPlan";
		        	 var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
		                // console.log(response);
		     			 if(response.data.status=="success")
		 				 {
		     				 $scope.success_message=response.data.data.message;
		     				 generate('success',response.data.data.message);
		     				 setTimeout(hideSuccessMessage ,3000);
		     				 $scope.errormessage='';
		     				
		     				
		 				 }
	     				else
		 				 {
		     				 $scope.errormessage=response.data.data.message;
		     				 generate('error',response.data.data.message);
		     				 setTimeout(hideSuccessMessage ,3000);
		     				 $scope.success_message='';
		 				 }	     			 
		     			$scope.loadingModel = false;
		      		 });
	        	}
	        	
	         }
	         
	         /******************************* Delete Payment Plan **********************/
	         
	        $scope.deletePaymentPlan=function(plan_id){
	        	 var planId = plan_id;
	        	 var usertoken=$window.localStorage.access_token;
	        	 console.log(planId);
	        	 var answer = confirm("Are you sure you want to delete?")
	 			if (answer) 
	 			{
	 				
	        		 
	        	 var credentials = {
	        			 	plan_id:planId,
	        			 	token:usertoken
	      			}
	        	 var Url="payment/deletePaymentPlan";
	        	 var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
	                // console.log(response);
	     			 if(response.data.status=="success")
	 				 {
	     				 $scope.success_message=response.data.data.message;
	     				 generate('success',response.data.data.message);
	     				 setTimeout(hideSuccessMessage ,3000);
	     				 $scope.errormessage='';
	     				for(var i = $scope.planArr.length - 1; i >= 0; i--){
	  						if($scope.planArr[i].id == planId){
	  							$scope.planArr.splice(i,1);
	  						}
	  					}
	     				
	     				
	 				 }
     				else
	 				 {
	     				 $scope.errormessage=response.data.data.message;
	     				 generate('error',response.data.data.message);
	     				 setTimeout(hideSuccessMessage ,3000);
	     				 $scope.success_message='';
	 				 }	     			 
	     			$scope.loadingModel = false;
	      		 });
	         }
	        }
	         
	     /****************** Send Reset Password Email **********************/
	        $scope.resetPasswordEmail=function(resetPasswordUserId)
	        {
	        	
	        	$("#reset_pwd_"+resetPasswordUserId).html('<span class="glyphicon glyphicon-refresh gly-spin"></span> Sending E-mail...');
	        	 var credentials = { user_id:resetPasswordUserId }
	        	 var Url="superadmin/sendResetPasswordEmail";
	        	 var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
                // console.log(response);
     			 if(response.data.status=="success")
 				 {
     				$("#reset_pwd_"+resetPasswordUserId).html('<i class="fa fa-envelope"></i> Reset Password');
     				 $scope.success_message=response.data.data.message;
     				 generate('success',response.data.data.message);
     				 setTimeout(hideSuccessMessage ,3000);
     				 $scope.errormessage='';
     			}
 				else
 				 {
 					$("#reset_pwd_"+resetPasswordUserId).html('<i class="fa fa-envelope"></i> Reset Password');
     				 $scope.errormessage=response.data.data.message;
     				 generate('error',response.data.data.message);
     				 setTimeout(hideSuccessMessage ,3000);
     				 $scope.success_message='';
 				 }	     			 
     			
      		 });
	        }
	        /****************** Getting the billing history of user  **********************/
	       
	        function getUserBillingHistory()
	        {
	        	$scope.PaymentHistory='';
	        	$scope.errormessage='';
	        	 var userId = $location.absUrl().split('/').pop();
		        	 var usertoken=$window.localStorage.access_token;
		        	 var credentials = { user_id:userId,token:usertoken }
		        	 var Url="payment/getUserAllPlan";
		        	 var registration_data = clients.getTemplateData(credentials, Url).then(function(response) {
	                // console.log(response);
	     			 if(response.data.status=="success")
	 				 {
	     				 //$scope.success_message=response.data.data.message;
	     				$scope.PaymentHistory=response.data.data.AllPlans;
	     				// console.log($scope.PaymentHistory);
	     				
	     			}
	 				else
	 				 {
	 					 $scope.errormessage="No payment history";
	     				// generate('error',response.data.data.message);
	     				// setTimeout(hideSuccessMessage ,3000);
	     				 //$scope.success_message='';
	 				 }	     			 
	     			
	      		 });
	        }
	         
	       
	         
	       var validityrange = [];
	       for(var i=1;i<200;i++) {
	    	   validityrange.push(i);
	        }
	        $scope.validityrange = validityrange;
	         
	        //************** Password Change functionality ********************//
            $scope.old_password='';
     		$scope.new_password='';
	    	$scope.confirm_password='';
	        $scope.updateAdminPassword = function()
	         { 
	        	$("#emailloader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/loader-img.gif"></div>');
	        	var old_password = $scope.old_password;
	        	var password = $scope.new_password;
	        	var confirm_password = $scope.confirm_password;
	        	if(password != confirm_password)
	        	{
	 				generate('error',"Password Does not Match");
					setTimeout(hideSuccessMessage ,3000);
	        		$scope.pass_error_message="Password Does not Match";
	        		return false;
	        	}
	        	else
	        	{   	
	        	var data={pass: password,old_pass: old_password,token: $window.localStorage.access_token};
	    		var Url="api/updatePassword";
	    		var updated_password = clients.getTemplateData(data, Url).then(function(response){
	    			if (response.data.status == 'success') {
	    				$("#emailloader").html('');
	    				$scope.pass_success_message="Your Password is successfully changed";
	    				generate('success',response.data.data.Message);
	    				setTimeout(hideSuccessMessage ,3000);
	    				$scope.old_password='';
	    	        	$scope.new_password='';
	    	        	$scope.confirm_password='';
	    	        	$scope.pass_error_message='';
	    			}
	    			else 
	     			{
	    				$("#emailloader").html('');
	     				generate('error',response.data.data.Message);
	    				setTimeout(hideSuccessMessage ,3000);
	    				$scope.pass_error_message= response.data.data.Message;
	    				pass_success_message='';
	    				$scope.old_password='';
	    	        	$scope.new_password='';
	    	        	$scope.confirm_password='';
	    	        	$scope.pass_success_message='';
	     			}
	    		});
	        	}
	         }
	         
	     
	     
	        function getCurrentUserDetail(){
	        	 var userDetail;
	 			var data={token: $window.localStorage.access_token};
	     		var Url="api/getUserDetail";
	     		var updated_password = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
	     			//console.log(successCallback.data.data.user.subdomain);
	     			//console.log(successCallback.data.data.user.email);
	     			if(successCallback.data.status=="success")
	 				{	
	     				//userDetail = successCallback.data.data.user;
	     				$scope.userdetail = successCallback.data.data.user;
	     				angular.forEach(successCallback.data.data.user,function(val,key){
	     					 $scope[key]=val;
	     				});
	     				if(angular.isDefined(successCallback.data.data.user.subdomain) && successCallback.data.data.user.subdomain !=''){
	         				$window.localStorage.subdomain=successCallback.data.data.user.subdomain;
	         				}
	         				else
	         					{
	         					$window.localStorage.subdomain='';
	         					}
	     				//$window.localStorage.subdomain=successCallback.data.data.user.subdomain;
	     				console.log(successCallback.data.data.user.subdomain);
	     				$scope.phone=parseInt(successCallback.data.data.user.contact_no);
	     				$scope.setloading=false;			
	 				}
	     			else
	 				{
	     				 $scope.errormessage=successCallback.data.data.message;
	    				 generate('error',successCallback.data.data.message);
	    				 setTimeout(hideSuccessMessage ,3000);
	    				// $state.go('userauth');
	    				 $location.path('/admin').replace();

	 				}
	     			
	     		});
	          }

	

	 $('#editadmindetailform').parsley();
	
	 $scope.myaccountSubmit=function(){ 
     	$scope.submitted=true;
     	 if($scope.frmadminsetting.$invalid)
			      return false;
     	//console.log($scope.subdomain); 
         var sub_domain=$scope.subdomain;
     	var email=$scope.email;
     	var firstname=$scope.first_name;
     	var lastname=$scope.last_name;
     	var contact=$scope.contact_no;
     	var data={token: $window.localStorage.access_token,email_id:email,subdomain:sub_domain,first_name:firstname,last_name:lastname,contact_number:contact};
 		var Url="api/updateProfileInfo";
 		var updated_domain = clients.getTemplateData(data, Url).then(function(successCallback,errorCallback){
 			//console.log(successCallback);
 			if(successCallback.data.status=="success")
				{
 				$scope.success_message=successCallback.data.data.message;
				 	generate('success',successCallback.data.data.message);
				 	setTimeout(hideSuccessMessage ,3000);
 			}
 			else
				{
 				 $scope.errormessage=successCallback.data.data.message;
 				 generate('error',successCallback.data.data.message);
 				 setTimeout(hideSuccessMessage ,3000);
 				 $location.path('/admin').replace();
				}
 			
 		});
     }

	 }	
