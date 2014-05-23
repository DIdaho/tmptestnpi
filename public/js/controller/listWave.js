function ctrlListWave($scope, $http, $routeParams, $injector, $filter, $q){

    $injector.invoke(ctrlList, this, {$scope: $scope});

    //Get list
    $http.get('npi/' + $routeParams.id + '/waves').success(function(data){
        $scope.list = data;
        $scope.edit(data[0]);
    });

    //Get activities
    $scope.activities = [];
    $http.get('activity').success(function(data){
        $scope.activities = data;
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

    /**
     * Load item before detail modal
     * @param item
     * @returns {HttpPromise}
     */
    $scope.loadingPromise = function(item){
        if(item && item._pk_wave)
        {
            return $http.get('wave/' + item._pk_wave).then(function(data){return data.data});
        }
        else
        {
            var deferred = $q.defer();
            deferred.resolve(item)
            return deferred.promise;
        }
    }

    //Activity selector
    $scope.select = {
        activity: null
    }

    /**
     * Add an activity
     */
    $scope.addActivity = function(){
        //Store ID
        var id = $scope.select.activity._pk_activity;

        //Reset selector
        $scope.select.activity = null;

        //Init item activities array
        if(!$scope.item.activities)
        {
            $scope.item.activities = [];
        }

        //If activity already in array, ignore
        if($filter('filter')($scope.item.activities, {_ke_activity: id}).length>0)
        {
            return;
        }

        //Add reference
        $scope.item.activities.push({
            _ke_activity: id,
            _ke_wave: $scope.item._pk_wave,
            order: 0
        });
    }

    /**
     * Remove an activity
     */
    $scope.removeActivity = function(){

    }
}