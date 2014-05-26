module.config(function($routeProvider){
    $routeProvider.
        when('/npi', {
            templateUrl: 'template/npi/list.html',
            controller: 'ctrlListNpi',
            reloadOnSearch: false
        }).
        when('/npi/:id', {
            templateUrl: 'template/wave/list.html',
            controller: 'ctrlListWave',
            reloadOnSearch: false
        }).
        when('/activity', {
            templateUrl: 'template/activity/list.html',
            controller: 'ctrlListActivity',
            reloadOnSearch: false
        }).
        when('/contact', {
            templateUrl: 'template/contact/list.html',
            controller: 'ctrlListContact',
            reloadOnSearch: false
        }).
        when('/mobile/:id?', {
            templateUrl: 'template/mobile/search.html',
            controller: 'ctrlMobileHome',
            reloadOnSearch: false
        }).
//        when('/', {
//            templateUrl: 'template/home.html',
//            reloadOnSearch: false
//        }).
        otherwise({
            redirectTo: '/npi'
        });
}).run(function($rootScope, $location, $http){
    //Load NPI
    $rootScope.npis = [];
    $http.get('npi').success(function(data){
        $rootScope.npis = data;
    })

    //Load config
    $rootScope.dictionary = {};
    $http.get('cpm-pos/dictionary').success(function(data){
        $rootScope.dictionary = data;
    })

});