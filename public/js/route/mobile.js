module.config(function($routeProvider){
    $routeProvider.
        when('/', {
            templateUrl: 'template/mobile/search.html',
            controller: 'ctrlMobile',
            reloadOnSearch: false
        }).
//        when('/', {
//            templateUrl: 'template/home.html',
//            reloadOnSearch: false
//        }).
        otherwise({
            redirectTo: '/'
        });
});