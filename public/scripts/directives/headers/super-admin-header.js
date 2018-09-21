'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('superadminheader',function(){
	
		return {
        templateUrl:'public/scripts/directives/headers/super-admin-header.html',
        restrict: 'E',
        replace: true,
    	}
	});


