'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('template',function(){
		return {
        templateUrl:'public/scripts/directives/templates/videofunnel-salesvideo.html',
        restrict: 'E',
        replace: true,
    	}
	});

