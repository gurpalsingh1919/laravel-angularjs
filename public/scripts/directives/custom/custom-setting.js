'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('customsetting',function(){
		return {
        templateUrl:'public/scripts/directives/custom/custom-setting.html',
        restrict: 'E',
        replace: true,
    	}
	});


