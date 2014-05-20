function ctrlNpiDetail($scope, $http){

    //Copy npi for edition
    $scope.item = angular.copy($scope.npi);

    //Save item
    $scope.save = function(){
        $http.put('npi/' + $scope.item._pk_npi, $scope.item).success(function(data){
            if($scope.npi)
            {
                angular.extend($scope.npi, data);
            }
            else
            {
                //Else add to list
                $scope.npis.push(data)
            }
            $scope.$hide();
        });
    }
}