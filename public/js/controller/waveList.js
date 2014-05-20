function ctrlWaveList($scope, $http, $routeParams, $modal){
    $http.get('npi/' + $routeParams.id + '/waves').success(function(data){
        console.log(data);
        $scope.waves = data;
    });
}