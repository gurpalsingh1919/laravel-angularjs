'use strict';

angular.module('authApp')
.directive('stateloadingindicator', function($rootScope,$timeout) {
  return {
    restrict: 'E',
    templateUrl: "public/assets/preview-template/common/loading.html",
   link : function (scope, elem, attrs) {
   $rootScope.isStateLoading = true;
      $timeout(function () {
        $rootScope.isStateLoading = false;
       // console.log(attrs.index);
		
      },8000);
    }
	   
    //link: function($scope, elem, attrs) {
     //$scope.isStateLoading = true;
	// console.log("hiiiiiiiiiiiii");

	/*var stateStart = $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState) {
	console.log("i am in start");
    var timeout = $timeout(function () {
        $ionicNavBarDelegate.title(findTitle(toState.name));
        $scope.isStateLoading = true;
    }, 50);
	console.log("yes1");
});


var stateFinish = $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams) {
console.log("i am in success");
    $timeout.cancel(timeout);
    $scope.isStateLoading = false;
	console.log("yes1");
});*/
	
	
	

     /*$scope.$on('$stateChangeStart', function() {
        $scope.isStateLoading = true;
		console.log("yes1");
      });
      $scope.$on('$stateChangeSuccess', function() {
        $scope.isStateLoading = false;
			console.log("yes2");
     
      });
    }*/
  };
});

