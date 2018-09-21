'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('loginafterheader',function(){
	
		return {
        templateUrl:'public/scripts/directives/headers/login-after-header.html',
        restrict: 'E',
        replace: true,
    	}
	});


