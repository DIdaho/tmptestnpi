function ctrlListActivity($scope, $http, $injector){

    $scope.config = {
        type: 'activity',
        fieldName: 'activ_title',
        title: 'Activity'
    }

    $injector.invoke(ctrlList, this, {$scope: $scope});

    $http.get('activity').success(function(data){
        $scope.list = data;
        $scope.edit(data[0])
    });
}