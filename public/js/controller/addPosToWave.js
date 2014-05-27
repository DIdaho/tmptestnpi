function ctrlAddPosToWave($scope, $http, $filter, promiseTracker){
    //Init pos array
    if(!$scope.item.pos)
    {
        $scope.item.pos = [];
    }

    var updatedLinked = function(){
        $scope.linked = _.map($scope.item.pos, function(value){return value.pos_apple_id});
    }

    //Promise trackers
    $scope.trackerSearch = promiseTracker();
    $scope.trackerAdding = promiseTracker();
    $scope.trackerRemoving = promiseTracker();

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

    /**
     * Launch search
     * @type {null}
     */
    $scope.results = null;
    $scope.search = function(){

        updatedLinked();

        $scope.results = null;
        var request = $http.post('cpm-pos/', $scope.filter).success(function(data){
            $scope.pagination.current = 1;
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
     * Select All/None
     */
    $scope.selectAllNone = function(){
        if($scope.results)
        {
            var checkAll = $filter('filter')($scope.results, {isSelected:true}).length != $scope.results.length;
            $scope.results.forEach(function(pos){pos.isSelected = checkAll});
        }
    }

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
     * Add a sales org
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
     * Remove item
     */
    $scope.removeItem = function(object, key){
        delete object[key];
    }

    /**
     * Add selected POS to the wave
     */
    $scope.addSelectedPos = function(){
        var toAdd = _.map(_.filter($scope.results, function(i){return i.isSelected}), function(pos){return pos.pos_apple_id});
        var request = $http.post('cpm-pos/add-pos-to-wave/' + $scope.item._pk_wave, toAdd).success(function(data){
            if(_.isArray(data))
            {
                $scope.item.pos = data;
                updatedLinked();
            }
        });
        $scope.trackerAdding.addPromise(request);
    }

    /**
     * Remove selected POS
     */
    $scope.removeSelectedPos = function(){
        var toRemove = _.map(_.filter($scope.results, function(i){return i.isSelected}), function(pos){return pos.pos_apple_id});
        var request = $http.delete('cpm-pos/delete-pos-from-wave/' + $scope.item._pk_wave, {
            data: toRemove
        }).success(function(data){
            if(_.isArray(data))
            {
                $scope.item.pos = data;
                updatedLinked();
            }
        });
        $scope.trackerRemoving.addPromise(request);
    }

    /**
     * Close modal
     */
    $scope.close = function(){
        $scope.$hide();
        $scope.modalDetail.show();
    }
}