'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('popups',function(){
		return {
        templateUrl:'public/scripts/directives/popup/popup.html',
        restrict: 'E',
        replace: true,
    	}
	});


