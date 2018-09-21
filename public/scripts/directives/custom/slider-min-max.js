'use strict';

/**
 * @ngdoc directive
 * @name izzyposWebApp.directive:adminPosHeader
 * @description
 * # adminPosHeader
 */
angular.module('sbAdminApp')
.directive('ngSliderOne', [function() {
  return {
    restrict: 'A',
    scope: {
      'model': '='
    },
    link: function(scope, elem, attrs) {
      var $slider = $(elem);
      $slider.slider({
        value: +scope.model,
		max: 20,
		step: 1,
        slide: function(event, ui) {
          scope.$apply(function() {
            scope.model = ui.value;
          });
        }
      });
    }
  };
}]);
