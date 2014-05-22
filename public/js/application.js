
/*
 ======== Module Definition ===========================
 */
var module = angular.module('Application', [
    'ngRoute',
    'ngSanitize',
//    'textAngular',
    'angular-growl',

    'mgcrea.ngStrap',
    'ui.sortable',
    'ajoslin.promise-tracker'
]).run(function($rootScope, $location, $http, promiseTracker){
    //Returns true if passed route(s) is active
    $rootScope.activeRoute = function(route){
        if(_.isArray(route))
        {
            return route.indexOf($location.path()) >= 0;
        }
        else
        {
            return $location.path().indexOf(route) >= 0;
        }
    };

    //Promise tracker
    $rootScope.tracker = promiseTracker();

});

// register the interceptor via an anonymous factory
module.factory('errorinterceptor', function ($q, growl) {
    function success(response) {
        return response;
    }

    function error(response) {
        if (response.data.message) {
            growl.addErrorMessage(response.data.message);
        }
        return $q.reject(response); //similar to throw response;
    }

    return function (promise) {

        return promise.then(success, error);
    }
});

module.directive('highlight', function($parse) {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {

            var highlight = $parse(attrs.highlight);

            scope.$watch(attrs.highlight, function(){
                if(ngModel.$viewValue)
                {
                    var h = highlight(scope);
                    element.html(ngModel.$viewValue.replace(new RegExp('('+h+')', 'i'), '<span class="bg-primary">$1</span>'));
                }
            })
        }
    }
})