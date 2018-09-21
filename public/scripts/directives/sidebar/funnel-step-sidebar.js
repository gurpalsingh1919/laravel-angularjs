'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('funnelsteps',function(){
		return {
        templateUrl:'public/scripts/directives/sidebar/funnel-step.html',
        restrict: 'E',
        replace: true,
    	}
	});


