'use strict';

	app.controller('funnelController', funnelController);

	function funnelController($scope,$rootScope, $state,$window,$location,$stateParams,clients,$sce,$fileUploader, $timeout,$interval) 
	{
		 $rootScope.isStateLoading = true;
		if(!angular.isDefined($window.localStorage.access_token) || $window.localStorage.access_token =='')
		{
			 $location.path('/login').replace();
		}
		//****************** Get template Data **************************//
		$scope.PageName=$window.localStorage.stepname;
		$scope.pagePath=$window.localStorage.funnelStepPath;
		//$scope.loading = true;
		$window.localStorage.notDefault='0';
		var tempslug = $location.absUrl().split('/').pop();
		$scope.domain_path = {'name':$location.absUrl().split('/')[0]};
		getTemplateCompleatData();
		//showTimer();
		$scope.AlreadysavedTemplateData=false;
		//****************** ngWYSIWYG Editor **************************//
		$scope.editorConfig = {
		           sanitize: true,
		           toolbar: [
		           { name: 'basicStyling', items: ['bold', 'italic', 'underline','leftAlign','centerAlign','rightAlign','blockJustify'] },
		           { name: 'colors', items: ['fontColor',  '-'] },
		           { name: 'styling', items: ['font', 'size', 'format'] },
		           ]
		    };
		
		//****************** close the template **************************//
		$scope.close_template = function(){ 
			if($scope.AlreadysavedTemplateData)
			{  
					 redirectBackToEditFunnel();
			}
			else
			{
				var answer = confirm("Are you sure you want to leave this page without Saving?")
				if (answer) {
				 redirectBackToEditFunnel();
				}
			}
			
		}

		
		
		function redirectBackToEditFunnel()
		{
			$window.localStorage.notDefault='1';
			var slug_url=$window.localStorage.funnel_id;
			var redirecturl="/editmyfunnel/"+slug_url;
			$location.path(redirecturl).replace();
			//$state.go($scope.slug_url,{'id':template_id}, { notify: false });
	         /*setTimeout(function() {
	            $window.location.reload();
	         }, 500);*/
		}

		
		//****************** Icon image **************************//
		$scope.logoArr = 
		 {'logo':'fa fa-picture-o',
				'link':'fa fa-link',
				'text':'fa fa-font',
				'clock':'fa fa-clock-o',
				'twitter':'fa fa-twitter',
				'facebook':'fa fa-facebook',
				'googleplus':'fa fa-google-plus',
				'lindin':'fa fa-linkedin',
				'video':'fa fa-video-camera'
		 }
		//****************** Unsort Associative array **************************//
		$scope.notSorted = function(obj){
			if (!obj) {
				return [];
			}
			return Object.keys(obj);
		}
		//****************** Check if child exists or not **************************//
		$scope.checkChild = function(id) {
	     if (typeof(id) == 'string') {
	         return true;
	     } else {
	         return false;
	     }
		   }
		//****************** closeModel function is use to close bootstrap model/popup **//
		function closeModel(type) {
		    if (type == "save_page_text") {
		        $('#myModal').modal('toggle');
		    } else if (type == "save_page_video") {
		        $('#videoModal').modal('toggle');
		    } else if (type == "countdown") {
		        $('#CountdownModal').modal('toggle');
		    }
		    else if (type == "save_seo_text") {
		        $('#mySeoPopup').modal('toggle');
		    }
		    else if (type == "save_tracking_text") {
		        $('#myTrackingCode').modal('toggle');
		    }
		    else if (type == "save_exit_popup_text") {
		        $('#myExitPopup').modal('toggle');
		    }
		    else if (type == "save_linking") {
		        $('#linkingModal').modal('toggle');
		    }
		    else if (type == "save_window_tab") {
		        $('#windowModal').modal('toggle');
		    }
		}
		//******************Show/Hide Functionality****************************//
			$scope.checkHideDiv = function(Val,Key) 
	        { 
				$scope.scopeKey=[];
				$scope.scopeKey=Key; 
	        	if($scope[$scope.scopeKey] == '1')
	    		{  
	    			$scope[$scope.scopeKey] = '0';
	    		}
	        	else
	    		{  
	    		  $scope[$scope.scopeKey] = '1';
	    		}
	        }
		//******************** Save text box value *******************************//
			$scope.saveTextEditorDetail= function(){
				var Value1=$scope.scopeValue;
	
				$scope[$scope.scopeKey] = $sce.trustAsHtml(Value1);
				closeModel('save_page_text');
			}
		//******************** Save seo popup value *****************//
			$scope.saveSeoDetail= function(){
				$scope.seo_page_title;
				$scope.seo_page_description;
				$scope.seo_page_keywords;
				
				closeModel('save_seo_text');
			}	
		//*********************Save Tracking Data***********************//
			$scope.saveTrackingDetail= function(){
				$scope.head_tracking_code;
				$scope.endofbodytag_tracking_code;
				
				closeModel('save_tracking_text');
			}
		//*********************Save Exit PopUp Data***********************//
			$scope.saveExitPopupDetail= function(){
				 $scope.exit_popup_mess;
				 $scope.exit_popup_url;
				 $scope.exit_popup_mess_no;
				 $scope.exit_popup_mess_yes;

				closeModel('save_exit_popup_text');
			}		
		//*********************** save video link *********************//
			$scope.saveVideoEditorDetail= function(){ 
				var Value1=$scope.scopeValue;
				$scope[$scope.scopeKey] = $sce.trustAsHtml(Value1);	
				closeModel('save_page_video');
			  
			}
		//*********************** save video link *********************//
			$scope.saveLinkingDetail= function(){
				var Value1=$scope.scopeValue;
				$scope[$scope.scopeKey] = $sce.trustAsHtml(Value1);	
				closeModel('save_linking');
			}
			
		//*********************** save video link with new tab option *********************//
		
			$scope.saveVideoWindowDetail= function(){
				var Value2=$scope.scopeValueWindow;
				var Value1=$scope.scopeValue; 
				$scope[$scope.scopeKey] = $sce.trustAsHtml(Value1);	
				$scope[$scope.scopeKeyWindow] = Value2;
				closeModel('save_window_tab');
			}		
			
		//*********************** show image popup *************************//
			 $scope.showImagePopup = function(scopVar){ 
				$scope.scopeValueImage = scopVar;
				}
			 $scope.changeImage = function(file_name,index){ 
				 	$scope.selected = 0;
					$scope.selected = index;
					$scope.selectedImage =file_name;	
				}
			 $scope.updateImageContent = function(){
				$scope[$scope.scopeValueImage] =$scope.selectedImage;
				}
			 
		$scope.checkImageData = function(Val, data, data1)  { 
			 $scope.scopeValue=[];
			 $scope.scopeKey=[];
			 var elementImage = angular.element('#imageModal');
			 if (data.indexOf('logo') > -1) 
			 {
				 elementImage.modal('show');
				 $scope.showImagePopup(Val);
				 $scope.scopeValue=Val;
				 $scope.image_name=data1;
			 }
		}

		//**********************Background Images**********************//
		$scope.backgorundImage = function(Val, data, data1)  { 
			 $scope.scopeValue=[];
			 $scope.scopeKey=[];
			 var elementBackgorundImage = angular.element('#backgroundImageModal');
			 if (data.indexOf('logo') > -1) 
			 {
				 elementBackgorundImage.modal('show');
				 $scope.showImagePopup(Val);
				 $scope.scopeValue=Val;
				 $scope.image_name=data1;				
			 }
		}
		//************************  This function is for left side popups ***************************//
		$scope.checkData = function(Val, data) 
		{ 
		    $scope.scopeValueWindow=[];
		    $scope.scopeKeyWindow=[];
		    $scope.scopeValue=[];
		    $scope.scopeKey=[];
			var elementText = angular.element('#myModal');
			var elementVideoLink = angular.element('#videoModal');
			var elementLinking = angular.element('#linkingModal');
			
			 if (data.indexOf('link') > -1) 
			 {
				 $("html").css('overflow','hidden');
				 $scope.home_sidebar= true;
				 $scope.ebook_editor_integration=false;
				 $scope.headertitle=false;
				 $scope.header_text=false;
				 $scope.custom_setting_status=true;
			 }
			 else if (data.indexOf('text') > -1) 
			 {
				 elementText.modal('show');
				 $scope.scopeValue =$sce.getTrustedHtml($scope[Val]);
				 $scope.scopeKey=Val;
				  $(".tinyeditor-buttons-group .tinyeditor-font option:first").text("Font Family");
				  $(".tinyeditor-buttons-group .tinyeditor-size option:first").text("Font Size");
				  $("select.tinyeditor-size:nth-child(3) option:first").text("Headings");
				
			 }
			 else if (data.indexOf('video') > -1) 
			 {
				 $scope.scopeValue =$sce.getTrustedHtml($scope[Val]);
				 $scope.scopeKey=Val;
				 elementVideoLink.modal('show');
				
			 }
			 if (data.indexOf('url') > -1) 
			 {
				 $scope.scopeValue =$sce.getTrustedHtml($scope[Val]);
				 $scope.scopeKey=Val;
				 elementLinking.modal('show');
			 }

		
	   }
		
		//******************Function to open link in new tab or not*******************//
		$scope.checkWindowData = function(Val, data, keydata) 
		{  
		    $scope.scopeValueWindow=[];
		    $scope.scopeKeyWindow=[];
		    $scope.scopeValue=[];
		    $scope.scopeKey=[];
		    var elementWindow = angular.element('#windowModal');
		   
			if (data.indexOf('tab') > -1) 
			{ 
				 $scope.scopeValue =$sce.getTrustedHtml($scope[Val]); 
				 $scope.scopeKey=Val;
				 
				 $scope.scopeValueWindow = $scope[keydata].toString(); 
				 $scope.scopeKeyWindow=keydata;       
				 
				 elementWindow.modal('show');
			}
			
		}


		//***************** Calender count down setting ***************************//
		function getValue() {
		    var val = $('#selectedDate').val();
		    return val;
		}
		$scope.SaveDetail = function(type){
			 if(type=="countdown"){
					var finalVal = getValue();
					var splitArr = finalVal.split('-');
					$scope.time_counter = splitArr[0]+'/'+splitArr[1]+'/'+splitArr[2];
					closeModel(type);
					countTimer($scope.time_counter);
				}
		}
		//******************* Update Countdown Scope **************//

		function countTimer(time_counter){
			$('#example').countdown({
				date: time_counter,
				offset: -8,
				day: 'Day',
				days: 'Days'
			});
		}
		

	
	    //***************** This function is for SEO popups *************************//
	     $scope.seoSettings = function() {
	    	 var elementSeo = angular.element('#mySeoPopup');
	    	 elementSeo.modal('show');
	    }
	    
	     //****************** This function is for Tracking popups ********************//
	    $scope.trackSettings = function() {
	    	var elementTracking=angular.element('#myTrackingCode');
	    	elementTracking.modal('show');
			 }
	 
	    //**************** This function is for Exitpopups *****************************//
		     $scope.initial = function()
		     {
		        $scope.status = false;
		     }
		     $scope.exitPopups = function() 
		     {
		    	 var elementExitPopup=angular.element('#myExitPopup');
		    	 elementExitPopup.modal('show');
		     }
		     $scope.changeExitPopupStatus = function()
		     {
		    	 if($scope.status == false)
		    	 {
		    		 $scope.status = true;
		    		 $scope.ExitPopupView = true;
		    	 } 
		    	 else if($scope.status == true)
		    	{
		    		 $scope.status = false;
		    		 $scope.ExitPopupView = false;
		    	}
		     }

	  
			   $scope.abc = function(scopVar)
			   { 
				 	 return Math.floor((Math.random() * 10000) + 1);
			   }
			   $rootScope.loadstatus = false;
			  // $rootScope.time_counter = 1451654351000;
		
		//*************************** File uploader ***********************************//
		var uploader='';
			var token = $window.localStorage.access_token;
			$("#imageuploader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/images/progressbar.gif"></div>');
			uploader = $scope.uploadimage = $fileUploader.create({
			scope: $scope,    
			url: 'admin/uploadImage',
			method: 'POST',
			formData: [
				{
					 key: 'value',
					tokenval : token
				
				}
			],
			filters: [
				function (item) {    
					return true;
				}
			]
		});
		//************************* Sending template id with uploader ********************//	 
	    uploader.bind('beforeupload', function (event, item) {
	    	 $(".imageuploader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/images/progressbar.gif"></div>');
		      item.formData = [{ templateid: $scope.templateId, templateslug: $scope.template_slug}];
				}); 
		uploader.bind('success', function (event, xhr, item, response) {
			 $(".imageuploader").html('');
			var parsedData = JSON.parse(response); 
			$scope.templateImage.push({id:parsedData.data.id,file_name:parsedData.data.name,image_type:parsedData.data.type});
			generate('success',parsedData.data.message_text);
			setTimeout(hideSuccessMessage ,3000);	
		});
		
		//********************** ADDING FILTERS **********************************//
        uploader.filters.push(function(item) {
            var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
            type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
            return '|jpeg|png|gif|jpg'.indexOf(type) !== -1;
        });
      //********************** Delete  Image **********************************//
  		$scope.deleteImage = function(img_id){
  			 var delimg= confirm("Do you want to delete this image?");
  			 if(delimg)
  			 {
  				var url='admin/deleteimage';
  				var data={imgid : img_id,tempid : $scope.templateId};
  				var user_login = clients.deleteData(data,url).then(function(response) {

  					//if (response.data.status == 'success') {
  					for(var i = $scope.templateImage.length - 1; i >= 0; i--){
  						if($scope.templateImage[i].id == img_id){
  							$scope.templateImage.splice(i,1);
  						}
  					}
  					 generate('success','Image deleted successfully');
  					 setTimeout(hideSuccessMessage ,3000);	
  					//}
  				});
  			 }
  			 else
  			 {
  				 return false;
  			 }
		}
  	//********************** Generate Iframe for video **********************************//
		/*function generateIframeMode($url)
		{
			$scope.youtube = $sce.trustAsHtml('<iframe width="650" height="400" src="'+$url+'" ></iframe>');
			//$scope.youtube = $sce.trustAsHtml($url);
		}*/
		
		 //************************ Show custom setting pop up ************************//
		 $scope.customSettingPopUp=function(status)
		 { 
			 //console.log(status);
			 if(status==1)
			 {
				 
				 $("html").css('overflow','hidden');
				
				 $scope.home_sidebar= true;
				 $scope.ebook_editor_integration=false;
				 $scope.headertitle=false;
				 $scope.header_text=false;
				 $scope.show_popup_form=true;
				 $scope.custom_setting_status=true;
				 $scope.not_optin=true;
				
			 }
			 else if(status==2)
			 {
				 $("html").css('overflow','hidden');
					
				 $scope.home_sidebar= true;
				 $scope.ebook_editor_integration=false;
				 $scope.headertitle=false;
				 $scope.header_text=false;
				 $scope.show_popup_form=false;
				 $scope.custom_setting_status=true;
				 $scope.not_optin=false;
				
			 }
			 else
			{
				 $("html").css('overflow','scroll');
				 $scope.custom_setting_status=false;
			}
				 	 
		 }

			
	   //*************************** API List Data********************************//
			
			var Url = 'admin/apilist';
			var token = $window.localStorage.access_token;
			var data={temp_slug : tempslug, Token : token};
			var template_data = clients.getTemplateData(data, Url).then(function(response) {
				$scope.autoResponder = response.data.data.autoResponder;
				if(angular.isDefined(response.data.data.autoResponder[0].name))
				{
					$scope.selectedItemvalue = response.data.data.autoResponder[0].name;
					$scope.selectedItem=response.data.data.autoResponder[0].name;
				}
				//$scope.autoresponder_list_val=response.data.data.autoResponder[0].api_list[0].name;
				
				$scope.ddlvalue="";
			});
			
			$scope.showSelectValue = function(mySelect) {

			     // console.log(mySelect);
			      $scope.autoresponder_list_val=mySelect;
			  }

			//****************** Publish the template **************************//
			$scope.publishTemplate=function()
			{    $('.headersavingloading').html('');
				SaveCompleteFunnelData();				
				$(".publish").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Publishing...');
				$window.localStorage.notDefault='1';
				var slug_url=$window.localStorage.funnel_id;
				var redirecturl="/editmyfunnel/"+slug_url;
				redirectUrl();
				var timer;
				function redirectUrl() {
				        timer = $timeout(function () {
				        	$location.path(redirecturl).replace();
				        }, 4500);
				    };
						
			};
			

		//********************* Save template data *******************************//
			
			$scope.save_template = function(){
				SaveCompleteFunnelData();	
			};
			function SaveCompleteFunnelData(){
				var url='admin/template';
		    $('.headersavingloading').html('<div class="loading publishloader"><img src="public/assets/admin/loader-img2.gif"><span>Please wait template is saving....</span></div>');		
			$("#save_template").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Saving...');
			
			//*********** Fetch data from template controller And then save in database**********//
			var pageDetailId= $window.localStorage.step_id;
			var data={temp_slug  : tempslug,page_detail_id:pageDetailId};
			var user_login = clients.getTemplateData(data,url).then(function(successCallback, errorCallback){ 
	
			var saveDetailArr = [];
			var saveStyleArr = [];
			var FinaltemplateArr = [];
			var template_id='';
			var thumbimg='';
			if(angular.isDefined(successCallback.data.data))
			{
				var rightarr=successCallback.data.data.rightArr;
				var stylearr=successCallback.data.data.styleArr
				var template_id=successCallback.data.data.template_id;
			}
			else
			{
				 var parsdata=JSON.parse(successCallback.data);
				 var rightarr=parsdata.data.rightArr;
				 var stylearr=parsdata.data.styleArr
				 var template_id=parsdata.data.template_id;
			}

			
			//******************* Assigning updated scope to the rightarr data **************//
				angular.forEach(rightarr, function (Val,Key) {
							angular.forEach(Val, function (Value,Keyy) {
								angular.forEach(Value, function (Value1,Key1) {
									var obj = { 'option_id' : Val['option_id'] };
									obj[Key1] = $sce.getTrustedHtml($scope[Key1]);
									saveDetailArr.push(obj);
								});
							}); 	
						}); 
			//******************** Assigning updated scope to the style array data **************//
				var i = 0;
				
				$scope.styleNewArr = stylearr;
				angular.forEach(stylearr, function (Val,Key) {
					if(i=='33'){$scope.styleArr[i]['default_value']=$scope.text_font_size;}
					if(i=='34'){$scope.styleArr[i]['default_value']=$scope.text_font_family;}
					if(i=='35'){$scope.styleArr[i]['default_value']=$scope.title_font_size;}
					if(i=='36'){$scope.styleArr[i]['default_value']=$scope.title_font_family;}
					//if(i=='37'){$scope.styleArr[i]['default_value']=$scope}
					
				saveStyleArr.push({'text':$scope.styleNewArr[i]['text'],'default_value':$scope.styleArr[i]['default_value'],'scopval':$scope.styleNewArr[i]['scopval']})
							i++;});

				FinaltemplateArr.push({'rightArr':saveDetailArr,
					'leftStyleArr':saveStyleArr,
					'page_detail_id':pageDetailId,
					'template_id':template_id,
					'token':$window.localStorage.access_token});
				
				$(".video-iframe iframe").replaceWith("<img src='public/assets/template/optin/images/video.png'>");
				html2canvas($('#takescreenshot'), { 
					onrendered: function(canvas) { 
						var thumbimg = canvas.toDataURL("image/png");
						var Parameters ={image: thumbimg, pageDetailId: $window.localStorage.step_id, templateId: template_id};
						var vidurl = $sce.getTrustedHtml($scope.vid_url);
						$(".video-iframe img").replaceWith(vidurl);
						var Url = 'admin/createThumbnailImages';	
						var template_data = clients.getTemplateData(Parameters,Url).then(function(successCallback, errorCallback) { 
							if(successCallback)
							{
								var Url = 'admin/save';	
								var template_data = clients.SaveTemplateData(FinaltemplateArr,Url).then(function(successCallback, errorCallback) {
									if(successCallback)
									{
										$scope.AlreadysavedTemplateData=true; 
										$("#save_template").removeAttr('disabled');
										$("#save_template").html('SAVE'); 
										generate('success','Template data has been saved successfully');
										setTimeout(hideSuccessMessage ,3000);
										$('.headersavingloading').html('<div class="loading publishloader"><img src="public/assets/admin/loader-img2.gif"><span>Please wait template is saving....</span></div>');
									}
									else
									{
										$("#save_template").removeAttr('disabled');
										$("#save_template").html('SAVE'); 
										generate('error','An error occur. Please Try again !');
										setTimeout(hideSuccessMessage ,3000);
									}				
								});
							}
							else
							{
								alert('image does not uploaded');
							}
							publishloader();
							function publishloader() {
							var timer = $timeout(function () {
							$('.headersavingloading').html('');
							}, 4700);
							}

						});
					}
				});
		});
			
			}
			
		//**************************** Get Template detail data *****************************//
			function getTemplateCompleatData()
			{

				var Url = 'admin/template';	
				var pageDetailId= $window.localStorage.step_id;
				var data={temp_slug : tempslug,page_detail_id: pageDetailId};
				var templatename=$location.absUrl().split('/').slice(-2)[0];
				  $scope.template_slug=templatename;
				$scope.ebook_image=[]; 
				$scope.header_section_id=true; $scope.banner_section_id=true;
				var template_data = clients.getTemplateData(data, Url).then(function(response) { 

				if (response.data.status == 'success') {
						$scope.leftArr = response.data.data.leftArr;
						$scope.styleArr = response.data.data.styleArr;
						$scope.rightArr = response.data.data.rightArr;
						/*if(angular.isDefined($scope.rightArr[2].value.vid_url))
						{
							$scope.vid_url = $scope.rightArr[2].value.vid_url;
						}*/
						$scope.templateId = response.data.data.template_id;
						$scope.templateImage = response.data.data.templateImages;
						
						angular.forEach(response.data.data.rightArr, function(Val, Key) {
							angular.forEach(Val, function(Value, Keyy) {
								angular.forEach(Value, function(Value1, Key1) {
									$scope[Key1] = $sce.trustAsHtml(Value1);    
								});

							});
						});
					
						
						if(angular.isDefined($scope.time_counter) && $scope.time_counter !='')
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
							})
						    
						});
						if(angular.isDefined($scope.styleArr['33']['default_value'])){$scope.text_font_size=$scope.styleArr['33']['default_value'];}
						if(angular.isDefined($scope.styleArr['34']['default_value'])){$scope.text_font_family=$scope.styleArr['34']['default_value'];}
						if(angular.isDefined($scope.styleArr['35']['default_value'])){$scope.title_font_size=$scope.styleArr['35']['default_value'];}
						if(angular.isDefined($scope.styleArr['36']['default_value'])){$scope.title_font_family=$scope.styleArr['36']['default_value'];}
						
						
						 $(".content-scroll").niceScroll();
					} 
					else 
					{
						alert("fails");
						$scope.isStateLoading=false;
						$location.path('/myfunnels').replace();
					}
				// $scope.loading = false;
				}); 
			}
			//************************ ebook data ******************************//
			$scope.ArrList = ['integration',
			                  'top_header_image',
			                  'header_text',
			                  'form_image',
			                  'headertitle',
			                  'button_title',
			                  'button_below_text',
			                  'input_name1',
			                  'input_name2',
			                  'input_name3'];
			//****************************** Pop up settings ************************//
			$scope.get_setting = function(status){ 
			 angular.forEach($scope.ArrList, function (arr) {
				if(arr==status){
						$scope[arr] = true;
					}
					else{
						$scope[arr] = false;
						}
				}); 
			}
			
		
			//****************************** Check default ************************//
			$scope.checkDefault = function(val,status){
				if((val==1 && status==1) || (val==0 && status==0)){
						return true;
					}else{
						return false;
					}
				}
			
			//****************************** Change header image ************************//

			$scope.changeHeaderImage = function(name,status,user_id,type_img,id){
				
				if(status==0){
						//var path = 'public/assets/admin/images/ebook-img/user_'+user_id+'/'+name;
					var ebookname = name;
					}else{
						//var path = 'public/assets/admin/images/ebook-img/'+name;
						var ebookname = name;
						}
					if(type_img=='header_img'){ 
						//$scope.$parent.defaultHeaderImage = path;	
					$scope.call_to_action_popup_image=ebookname;
					}else{
						//$scope.$parent.defaultformImage = path;	
						$scope.call_to_action_popup_image=ebookname;
						}	
				}
		
			$scope.deleteEbookImage = function(img_id){
				var temp_id= $scope.templateId; 
				var data={imgid : img_id,tempid : temp_id};
				var user_login = clients.deleteData(data,'admin/ebookdeleteimage').then(function(response) {
					for(var i = $scope.ebook_image.length - 1; i >= 0; i--){
						if($scope.ebook_image[i].id == img_id){
							$scope.ebook_image.splice(i,1);
						}
					}
					 generate('success','Image deleted successfully');
					 setTimeout(hideSuccessMessage ,3000);	
				});
			}
			
			
			//***********Function call on save and cancel of custom setting popup*************//
			 $scope.customPopUpSave=function(status)
			 { 
				 if(status==1)
				 {
					 
					 $("html").css('overflow','hidden');
					 $scope.home_sidebar= true;
					 $scope.ebook_editor_integration=false;
					 $scope.headertitle=false;
					 $scope.header_text=false;
					 $scope.custom_setting_status=true;
					 $scope.form_image=false;
					 $scope.input_name1=false;
					 $scope.input_name2=false;
					 $scope.input_name3=false;
					 $scope.button_title=false;
					 $scope.button_below_text=false;
					 $scope.thank_you=false;
				 }
		    }

				
		 //********************************Reload List**********************************//
				$scope.reloadList = function(id)
				{
					$("#reloading-div").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Reloading...');
					$("#Reload").html('');
					var Url = 'admin/reloadapilist';
					var data={temp_slug : tempslug,api_id: id};
					var template_data = clients.getTemplateData(data, Url).then(function(response) {
						//$("#reloading-div").html('Reload');
						//$scope.api_list = response.data.data.autoResponderRefresh;
						$scope.autoResponder= response.data.data.autoResponderRefresh;
						$scope.autoResponder_selected=id;
						
						//$scope.selectedItemvalue ="";
					});
				}
   }
	

//********************************* Ebook Controller *******************************************************//
	app.controller('eBooksController', eBooksController);
		
	function eBooksController($scope,$rootScope, $state,$window,$location,$stateParams,clients,$sce,$fileUploader)
	{
		if(!angular.isDefined($window.localStorage.access_token))
		{
			 $location.path('/login').replace();
		}		
		//*****************************ebook Image upload ***************************************/
		var token = $window.localStorage.access_token;
		var uploader = $scope.uploader = $fileUploader.create({
			scope: $scope,    
			url: 'admin/ebookimageupload',
			method: 'POST',
			formData: [
				{
					 key: 'value',
					tokenval : token
				}
			],
			filters: [
				function (item) {    
					return true;
				}
			]
		});	
		
		//Sending id of template with uploader
		 uploader.bind('beforeupload', function (event, item) {
			 $(".custompopimgload").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/images/progressbar.gif"></div>');
			 item.formData = [{templateid: $scope.$parent.templateId}];
					});
		 // Adding filter for register button Images
	uploader.filters.push(function(item) {
		var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
		type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
		return '|jpeg|png|gif|jpg'.indexOf(type) !== -1;
	});
	
	// Register Handler for register button Images
			uploader.bind('success', function (event, xhr, item, response) {
				var parsedData = JSON.parse(response); 
				 $(".custompopimgload").html('');
				if(parsedData.status=='success'){
					$scope.$parent.ebook_image.push({
						user_id:parsedData.data.user_id,
						img_name:parsedData.data.name,
						isDefault:parsedData.data.is_default,
						id:parsedData.data.id
						});
					generate('success',parsedData.data.message_text);
					setTimeout(hideSuccessMessage ,3000);
				}
				else
				{
					generate('error','File too large. File must be less than 5 megabytes.');
					setTimeout(hideSuccessMessage ,3000);	
				}
				 
			});
			uploader.bind('error', function (event, xhr, item, response) {
					var parsedData = JSON.parse(response);
					generate('error',parsedData.data.message_text);
					setTimeout(hideSuccessMessage ,3000);	
			});
	
}
	
	//********************************* Background Image Controller *******************************************************//
	app.controller('backgroundImageController', backgroundImageController);
		
	function backgroundImageController($scope,$rootScope, $state,$window,$location,$stateParams,clients,$sce,$fileUploader)
	{
		if(!angular.isDefined($window.localStorage.access_token))
		{
			 $location.path('/login').replace();
		}
		var token = $window.localStorage.access_token;
		var uploader = $scope.uploader = $fileUploader.create({
			scope: $scope,    
			url: 'admin/backgroundimageupload',
			method: 'POST',
			formData: [
				{
					 key: 'value',
					tokenval : token
				}
			],
			filters: [
				function (item) {    
					return true;
				}
			]
		});	
		
		//Sending id of template with uploader
		 uploader.bind('beforeupload', function (event, item) {
			 $(".imageuploader").html('<div ng-show="loading" class="loading"><img src="public/assets/admin/images/progressbar.gif"></div>');
			 item.formData = [{templateid: $scope.$parent.templateId, templateslug: $scope.$parent.template_slug}];
					});
		 // Adding filter for register button Images
	uploader.filters.push(function(item) {
		var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
		type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
		return '|jpeg|png|gif|jpg'.indexOf(type) !== -1;
	});
	
	// Register Handler for register button Images
			uploader.bind('success', function (event, xhr, item, response) {
				var parsedData = JSON.parse(response);
				$(".imageuploader").html('');
				//$scope.$parent.top_background_image = parsedData.data.name; 
				$scope.$parent.templateImage.push({file_name:parsedData.data.name,id:parsedData.data.id,image_type:parsedData.data.type});
					generate('success',parsedData.data.message_text);
					setTimeout(hideSuccessMessage ,3000);				 
			});
			uploader.bind('error', function (event, xhr, item, response) {
					var parsedData = JSON.parse(response);
					generate('error',parsedData.data.message_text);
					setTimeout(hideSuccessMessage ,3000);	
			});
	}
	     		

