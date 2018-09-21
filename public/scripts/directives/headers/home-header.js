'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('loginbeforeheader',function(){
		return {
        templateUrl:'public/scripts/directives/headers/home-header.html',
        restrict: 'E',
        replace: true,
    	}
	});


