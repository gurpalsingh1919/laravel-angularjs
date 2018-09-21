'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('sidebar',function(){
		return {
        templateUrl:'public/scripts/directives/sidebar/sidebar.html',
        restrict: 'E',
        replace: true,
    	}
	});


