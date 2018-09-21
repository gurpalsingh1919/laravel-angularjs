'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('mysettings',function(){
		return {
        templateUrl:'public/scripts/directives/settings/myaccountsetting.html',
        restrict: 'E',
        replace: true,
    	}
	});


