function ctrlNpiList($scope, $http, $location, $modal){

    /**
     * Edit NPI
     * @param npi
     */
    $scope.edit = function(npi){
        var scope = $scope.$new();
        scope.npi = npi;
        $modal({
            template: 'template/npi/detail.html',
            title: npi ? npi.npi_label:'New NPI',
            scope: scope
        });
    }

    /**
     * View wave list
     * @param npi
     */
    $scope.open = function(npi){
        $location.path('/npi/' + npi._pk_npi);
    }
}