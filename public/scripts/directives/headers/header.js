'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('header',function(){
		return {
        templateUrl:'public/scripts/directives/headers/header.html',
        restrict: 'E',
        replace: true,
    	}
	});


