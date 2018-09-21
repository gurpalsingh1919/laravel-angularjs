app.factory('clients', ['$http',function($http) {  
 return {

  
 getTemplateData: function($data,$url) {    
	promise = $http.post($url,$data);
	return promise;
  },
 deleteData: function($data,$url) {
	promise = $http.post($url,$data);
	return promise;
  },
 getAweberData: function($data,$url) {
	promise = $http.post($url,$data);
	return promise;
  },
 getAweberUserList: function($data,$url){
	  promise=$http.post($url,$data)
	  return promise;
  },
SaveTemplateData: function($data,$url) {
	promise = $http.post($url,$data);
	return promise;
},
CategoryDetailData: function($data,$url) {
	promise = $http.post($url,$data);
	return promise;
},
getUsersData: function($url) {    
	promise = $http.post($url);
	return promise;
},
  
}
}]);
app.filter('trustUrl', function($sce) {
	  return function(url) {
	    return $sce.trustAsResourceUrl(url);
	  };
	});
function generate(type, message) {
    var html = '<span style="color:white;font-weight: bold;"><i class="fa fa-check-circle" style=" margin-right: 5px;"></i>' + message + '</span>';
    var n = noty({
        text: html,
        type: type,
        dismissQueue: true,
        layout: 'topLeft',
        closeWith: ['click'],
        theme: 'relax',
        maxVisible: 10,
        animation: {
            open: 'animated bounceInLeft',
            close: 'animated bounceOutLeft',
            easing: 'swing',
            speed: 200
        }
    });
}

function hideSuccessMessage() {
	$.noty.closeAll();
    //console.log("hi im there");
}
app.filter('format', function () {
    return function (item) {
    	var millisecondsCurrentDate = new Date().getTime();
    	var t = item.split(/[- :]/);
    	var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    	var milliSecondPreviousDate=d.getTime(); 
    	var one_day=1000*60*60*24;    // Convert both dates to milliseconds
    	var difference_ms = millisecondsCurrentDate - milliSecondPreviousDate;        // Convert back to days and return   
    	var days=Math.round(difference_ms/one_day); 
    	if(days==0)
		{
    		var  s = Math.floor(difference_ms / 1000);
    		var m = Math.floor(s / 60);
       	  	var h = Math.floor(m / 60);
       	  		h = h % 24;
   	  		if(h==0)
  			{
   	  			if(m==0)
   	  			{
   	  				return s +' seconds';
   	  			}
   	  			else
  				{
   	  				return m +' minutes';
  				}
   	  			
  			}
   	  		else
  			{
   	  			return h +' hrs';
  			}
       	  	
		}
    	else
		{
    		return days +' days';
		}
    	
    
    };
  });

app.service('MetadataService', ['$window', function($window){
	 var self = this;
	 self.setMetaTags = function (tagData){
	   $window.document.getElementsByName('keyword')[0].content = tagData.keyword;
	   $window.document.getElementsByName('description')[0].content = tagData.description;
	   $window.document.getElementsByName('title')[0].content = tagData.title;
	 }; 
	}]);


app.directive("datepickers", function () {
	  return {
	    restrict: "A",
	    require: "ngModel",
	    link: function (scope, elem, attrs, ngModelCtrl) {
	      var updateModel = function (dateText) {
	        scope.$apply(function () {
	          ngModelCtrl.$setViewValue(dateText);
	        });
	      };
	      var options = {
	        dateFormat: "dd/mm/yy",
	        onSelect: function (dateText) {
	          updateModel(dateText);
	        }
	      };
	      elem.datepicker(options);
	    }
	  }
	});


app.directive('toggleClass', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            element.bind('click', function() {
                var str = element.attr("class");
                var str =  str.indexOf("glyphicon-eye-open");
                if(str > 1) 
                {
                    element.removeClass("glyphicon-eye-open");
                    element.addClass("glyphicon-eye-close");
                    //element.addClass(attrs.toggleClass);
                } 
                else
                {
                    element.removeClass("glyphicon-eye-close");
                    element.addClass("glyphicon-eye-open");
                }
            });
        }
    };
});

		//******Dynamic height to div*********//
		app.directive('setHeight', function($window){
			  return{  
			    link: function(scope, element, attrs){
			        element.css('min-height', $window.outerHeight + 'px');
			        //element.height($window.innerHeight/3);
			    }
			  }
		}); 

	/*	app.filter('myCurrency', ['$filter', function ($filter) {
			  return function(input) {
			   // input = parseFloat(input);
			    return input.replace(/[^\/\d]/g,'');
			    /*if(input % 1 === 0) {
			      input = input.toFixed(0);
			    }
			    else {
			      input = input.toFixed(2);
			    }

			    return input.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			  };
			}]);  */
  
