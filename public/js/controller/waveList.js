function ctrlWaveList($scope, $http, $routeParams, $injector){

    $injector.invoke(ctrlList, this, {$scope: $scope});

    $http.get('npi/' + $routeParams.id + '/waves').success(function(data){
        console.log(data);
        $scope.list = data;
    });

    /**
     * Set default values when insert/update
     * @param item
     */
    $scope.setDefaultValues = function(item){
        if( ! item._ke_npi)
        {
            item._ke_npi = $routeParams.id;
        }
    }
}