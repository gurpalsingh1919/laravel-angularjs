'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('changepassword',function(){
		return {
        templateUrl:'public/scripts/directives/settings/changepassword.html',
        restrict: 'E',
        replace: true,
    	}
	});


