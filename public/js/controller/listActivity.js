function ctrlListActivity($scope, $http, $injector){

    $scope.config = {
        type: 'activity',
        fieldName: 'activ_label',
        title: 'Activity'
    }

    $injector.invoke(ctrlList, this, {$scope: $scope});

    $http.get('activity').success(function(data){
        console.log(data);
        $scope.list = data;
    });
}