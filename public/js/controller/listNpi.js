function ctrlListNpi($scope, $injector, $location, $modal){

    $scope.config = {
        type: 'npi',
        fieldName: 'npi_label',
        title: 'NPI'
    }

    $injector.invoke(ctrlList, this, {$scope: $scope});

    /**
     * View wave list
     * @param npi
     */
    $scope.open = function(npi){
        $location.path('/npi/' + npi._pk_npi);
    }

    $scope.$watchCollection(function(){return $scope.npis}, function(){
        $scope.list = $scope.npis;
    })
}