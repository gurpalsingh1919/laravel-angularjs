'use strict';
	app.controller('previewController', previewController);
	
	function previewController($scope, $auth, clients, $sce, $state, $window, $location, $rootScope, $http, MetadataService) 
	{
		//$window.onload = function(e) {
		$scope.protocol=$location.protocol()+"://";
		 $rootScope.isStateLoading = true;
			//}
		 
		function countTimer(timecounter){
			$('#example').countdown({
				date: timecounter,
				offset: -8,
				day: 'Day',
				days: 'Days'
			});
		}
		var pagedetailsid;
		var url="myfunnel/getCreatedFunnel";
		var secondlast=$location.absUrl().split('/').slice(-2)[0];
		//var pageDetailId= $window.localStorage.stepId;
		var tempslug = $location.absUrl().split('/').pop();
		//********************* //
		var new_old='old';
		  if(localStorage.getItem (tempslug))
		  {
	            //localStorage.hits = Number(localStorage.hits) +1;
	      }
	      else
	      {
	    	  localStorage.setItem (tempslug, tempslug);
	    	  new_old='new';
	       }
		//alert(tempslug);
		 
		var data={new_temp_slug_url  : tempslug,uniqueHit : new_old};
		$scope.ebook_image=[];
		var user_login = clients.getTemplateData(data,url).then(function(response){
			//alert(response.data.data.browser);
			if(angular.isDefined(response.data.data.page_detail_id))
			{
				$scope.page_detail_id=response.data.data.page_detail_id;
				pagedetailsid=response.data.data.page_detail_id;
			}
			if (response.data.status == 'success') {
				
				//$scope.leftArr = response.data.data.leftArr;
				$scope.styleArr = response.data.data.styleArr;
				$scope.rightArr = response.data.data.rightArr;
				if(angular.isDefined($scope.rightArr[2].value.vid_url))
				{
					var htmlData=$scope.rightArr[2].value.vid_url;
					$scope.vid_url =$sce.trustAsHtml(htmlData);
				}
				//generateIframeMode($scope.vid_url);
				$scope.templateId=response.data.data.template_id;
				$scope.templateImage = response.data.data.templateImages;
				angular.forEach(response.data.data.rightArr, function(Val, Key) {
					angular.forEach(Val, function(Value, Keyy) {
						angular.forEach(Value, function(Value1, Key1) {
							$scope[Key1] = $sce.trustAsHtml(Value1);    
						});
					});
				});
				if(angular.isDefined($scope.time_counter))
				{
					var timecounter=$scope.time_counter.toString();
					 countTimer(timecounter);
				}
				angular.forEach(response.data.data.ebookImages, function(ArrValue, ArrKey) {
					$scope.ebook_image.push({
						user_id: ArrValue.user_id,
						img_name: ArrValue.file_name,
						isDefault: ArrValue.is_default,
						id: ArrValue.id
					});
				});
				
				var btn_text=$scope.call_to_action_button_title;
				$scope.registered='0';
				//setMetaTagAndSeo();
				$scope.loading=false;
			} 
			else 
			{
				$location.path('/login').replace();
			} 
		});
		
	//***************************** Alert When close tab **********************//
	function setMetaTagAndSeo()
	{
		 MetadataService.setMetaTags({
		       keyword: $scope.seo_page_keywords,
		       description: $scope.seo_page_description,
		       title:$scope.seo_page_title,
		    });
	}
	
	
	$scope.Checklinked=function(key, Keytab)
	{
		var link=$scope[key].toString(); 
		var tab=$scope[Keytab].toString(); 
		if(link !='')
		{
			//var url = $location.protocol() + '://'+ link;
			var url=addhttp(link);
			//alert(url);
			openlink(url,tab)
		}
	}
	function addhttp(url) {
		 if (!/^(?:f|ht)tps?\:\/\//.test(url)) {
	    	url = "http://" + url;
	    }
	    return url;
	}
	function openlink(url,tabinfo)
	{
		if(tabinfo=='0')
		{
			window.open(url,'_self');
		}
		else
		{
			//alert(url);
			window.open(url,'_blank');
		}
	}
	
	/*window.addEventListener("beforeunload", function (e) {
		var popit=$scope.exit_popup_mess_yes;
		//var exit_popup_mess=$scope.exit_popup_mess;
		  var confirmationMessage = $scope.exit_popup_mess;
		  if(popit=='yes' && 	$scope.registered !='1')
		  {
			  e.returnValue = confirmationMessage;     // Gecko, Trident, Chrome 34+
			  return confirmationMessage;              // Gecko, WebKit, Chrome <34
		  }
		 
		});
	*/
		//****************************** Register optin user ************************//
		var elementImage = angular.element('#linkModal');
		$scope.callbtn=function(){
			 //console.log(elementImage);
			//if($scope.registered !='1')
			//{
				 elementImage.modal('show');
			//}
			
			// console.log("1");
		}
		$scope.CallToActionformSubmit=function(){
			
			$scope.submitted=true;
			 if($scope.frmRegister.$invalid)
			 { console.log('if');
				 return false;
			 }
			 else
			 {  console.log('else');  
			 $("#addcontact").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Saving please wait...');
			 $scope.loadingModel=true; 
			var url="myfunnel/SubscribeNewUser";
			if($scope.username){var name=$scope.username;}else{var name='';}
			if($scope.email){var email=$scope.email;}else{var email='';}
			if($scope.phoneno){var phoneno=$scope.phoneno;}else{var phoneno='';}
			var page_detail_id=$scope.page_detail_id; 
			if(angular.isDefined($scope.autoResponder_selected) && angular.isDefined($scope.autoresponder_list_val))
			{
				 var apiName=$scope.autoResponder_selected.toString();
				 var api_list=$scope.autoresponder_list_val.toString();
			}
			else
			{
				 var apiName='';
				 var api_list='';
			}			
			var data={username  : name,useremail:email,usernumber:phoneno,pageDetailId:page_detail_id,api_name : apiName,list_id:api_list};
			optinUserRegistration(data,url);
			}
		}
		function optinUserRegistration(data,url)
		{
			var user_login = clients.getTemplateData(data,url).then(function(response){
				//console.log(response);
				if (response.data.status == 'success') 
				{
					 var tanku_page=$scope.thank_you_link.toString();
					$scope.registered='1';
					if(tanku_page !='')
					{
						window.location.href = $location.protocol() + '://'+ tanku_page;
					}
					else
					{
						generate('success',response.data.data.message);
						//setTimeout(hideSuccessMessage ,3000);
						 elementImage.modal('hide');
					}
					setTimeout(hideSuccessMessage ,3000);
					$("#addcontact").html("You are registered!");
					$scope.loadingModel=false;
				}
				else
				{
					generate('error',response.data.data.message);
					setTimeout(hideSuccessMessage ,3000);
					elementImage.modal('hide');
					
				}

			});
		}
		
		
		
		}
	
	
