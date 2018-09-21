'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('mysettingsidebar',function(){
		return {
        templateUrl:'public/scripts/directives/sidebar/mysetting-sidebar.html',
        restrict: 'E',
        replace: true,
    	}
	});


