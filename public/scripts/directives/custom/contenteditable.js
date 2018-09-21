'use strict';

/**
 * @ngdoc directive
 * @name contenteditable.directive:contenteditable
 */
angular.module('sbAdminApp')
.directive('contenteditable', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ctrl) {
      element.bind('click', function() {
		  $('#header_template').css('display','none');
		  $('#header_editor').css('display','block');
	  });
      //~ ctrl.$render = function() {
		
        //~ element.html(ctrl.$viewValue);
      //~ };
      //~ ctrl.$render();
    }
  };
});
