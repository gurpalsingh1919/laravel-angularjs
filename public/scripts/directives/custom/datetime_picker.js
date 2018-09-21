'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('authApp')
	.directive('datetimez',function(){
		 return {
        restrict: 'A',
        link: function(scope, element, attrs, ngModelCtrl) {
            $(function(){
                element.datetimepicker({
                    inline: true,
					format: 'YYYY-MM-DD HH:mm:ss',
					sideBySide: false,
                   
                });
            });
        }
    };
	});
