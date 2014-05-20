function ctrlNpiList($scope, $http, $routeParams, $modal){

    $scope.open = function(npi){
        var scope = $scope.$new();
        scope.npi = npi;
        $modal({
            template: 'template/npi/detail.html',
            title: npi.npi_label,
            scope: scope
        });
    }
}