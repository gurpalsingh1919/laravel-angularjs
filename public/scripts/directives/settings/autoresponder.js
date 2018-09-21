'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('autoresponder',function(){
		return {
        templateUrl:'public/scripts/directives/settings/autoresponder.html',
        restrict: 'E',
        replace: true,
    	}
	});


