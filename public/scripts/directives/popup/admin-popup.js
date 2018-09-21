'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('adminpopups',function(){
		return {
        templateUrl:'public/scripts/directives/popup/admin-popup.html',
        restrict: 'E',
        replace: true,
    	}
	});


