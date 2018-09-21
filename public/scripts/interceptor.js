'use strict';

	app.controller('imageUploader', imageUploader);
//For upload image and delete image..

//app.controller('imageUploader', ['$scope','$rootScope','$location','$window','$fileUploader',
   function imageUploader($scope,$rootScope,$ocation,$window,$fileUploader) {
			var token = $window.sessionStorage.access_token;
			var uploader = $scope.uploader = $fileUploader.create({
				scope: $scope,    
				url: 'admin/upload',
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
	
		}
//]);
