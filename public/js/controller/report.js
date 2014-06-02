function ctrlReport($scope, $http, $routeParams){
    //Scope variables
    $scope.list = [];
    $scope.activities = {};
    $scope.wave = {};

    //Init pagination
    $scope.pagination = {
        current: 1,
        nbItems: 20
    };

    //Get POS list
    $http.get('cpm-pos/stored/' + $routeParams.id, {tracker: $scope.tracker}).success(function(data){
        $scope.list = data;
    });

    //Get Wave info
    $http.get('wave/' + $routeParams.id, {tracker: $scope.tracker}).success(function(data){
        $scope.wave = data;
    });

    //Get Activities info
    $http.get('activity', {tracker: $scope.tracker}).success(function(data){
        $scope.activities = _.indexBy(data, '_pk_activity');
    });
}