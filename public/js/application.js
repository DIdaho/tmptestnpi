
/*
 ======== Module Definition ===========================
 */
var module = angular.module('Application', [
    'ngRoute',
    'ngSanitize',
//    'textAngular',
//    'angular-growl',

    'mgcrea.ngStrap',
    'ui.sortable',
    'ajoslin.promise-tracker'
])
.config(function($routeProvider){
    $routeProvider.
        when('/npi/:templateId?', {
            templateUrl: 'template/npi/list.html',
            controller: 'ctrlNpi',
            reloadOnSearch: false
        }).
//        when('/report', {
//            templateUrl: 'modules/coverage/template/reportInstance.html',
//            controller: 'ctrlReportInstance',
//            reloadOnSearch: false
//        }).
//        when('/setting', {
//            templateUrl: 'modules/coverage/template/reportParameter.html',
//            controller: 'ctrlReportParameter',
//            reloadOnSearch: false
//        }).
//        when('/', {
//            templateUrl: 'modules/coverage/template/home.html',
//            controller: 'ctrlHome',
//            reloadOnSearch: false
//        }).
        otherwise({
            redirectTo: '/'
        });
}).run(function($rootScope, $location, $http, promiseTracker){
    //Global config
    $rootScope.config = {

    };

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