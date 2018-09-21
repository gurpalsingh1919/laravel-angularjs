'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('previewpopups',function(){
		return {
        templateUrl:'public/scripts/directives/popup/preview-popup.html',
        restrict: 'E',
        replace: true,
    	}
	});


