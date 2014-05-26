function ctrlAddPosToWave($scope, $http, $filter, promiseTracker){
    //Init pos array
    if(!$scope.item.pos)
    {
        $scope.item.pos = [];
    }

    //Search promise tracker
    $scope.trackerSearch = promiseTracker();

    //Init pagination
    $scope.pagination = {
        current: 1,
        nbItems: 10,
        maxItems: 0
    };

    //Init filter
    $scope.filter = {
        rtm: {},
        program: {},
        region: null,
        countries: {},
        salesorgs: {}
    };

    //Init search object
    $scope.search = {
        country: null
    };

    $scope.results = null;
    $scope.search = function(){
        var request = $http.post('cpm-pos/', $scope.filter).success(function(data){
            $scope.sql = data.sql;
            if(data.results)
            {
                $scope.results = data.results;
                $scope.pagination.maxItems = data.results.length;
            }
        });
        $scope.trackerSearch.addPromise(request);
    };

    /**
     * Add a country
     */
    $scope.addCountry = function(){
        var country = $scope.search.country;
        var list = $filter('filter')($scope.dictionary.countries, {name: country});
        if(list.length>0 && list[0].name == country)
        {
            $scope.search.country = null;
            $scope.filter.countries[country] = true;
        }
    };

    /**
     * Add a country
     */
    $scope.addSalesOrg = function(){
        var salesorg = $scope.search.salesorg;
        var list = $filter('filter')($scope.dictionary.salesorg, {name: salesorg});
        if(list.length>0 && list[0].name == salesorg)
        {
            $scope.search.salesorg = null;
            $scope.filter.salesorgs[salesorg] = true;
        }
    }

    /**
     * Remove country
     */
    $scope.removeItem = function(object, key){
        delete object[key];
    }

    /**
     * Close modal
     */
    $scope.close = function(){
        $scope.$hide();
        $scope.modalDetail.show();
    }
}