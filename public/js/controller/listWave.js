function ctrlListWave($scope, $http, $routeParams, $injector, $filter, $q, $modal){

    $injector.invoke(ctrlList, this, {$scope: $scope});

    //Init pagination
    $scope.pagination = {
        current: 1,
        nbItems: 10
    };

    //Activities
    $scope.activities = [];

    //Activity selector
    $scope.select = {
        activity: null
    }

    //Current NPI primary key
    $scope.currentPkNpi = $routeParams.id;

    //Get list
    $http.get('npi/' + $routeParams.id + '/waves', {tracker: $scope.tracker}).success(function(data){
        $scope.list = data;
//        $scope.edit(data[0]);
    });

    //Get activities list
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
            return $http.get('wave/' + item._pk_wave).then(function(data){

                var object = data.data;
                return $http.get('cpm-pos/stored/' + item._pk_wave).then(function(data){
                    object.pos = data.data;

                    //Return detailed object
                    return object;
                });
            });
        }
        else
        {
            var deferred = $q.defer();
            deferred.resolve(item)
            return deferred.promise;
        }
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
    $scope.removeActivity = function(index){
        $scope.item.activities.splice(index, 1);
    }

    /**
     * Open modal to edit POS
     */
    $scope.openAddPosToWave = function(){
        $scope.modalDetail.hide();
        $modal({
            template: 'template/wave/addPosToWave.html',
            scope: $scope,
            controller: ctrlAddPosToWave,
            backdrop: 'static',
            keyboard: false
        });
    }

    /**
     *
     * @param wave
     * @param status
     */
    $scope.setStatus = function(wave, status){
        $http.put('wave/' + wave._pk_wave , {
            wave_status: status
        }).success(function(){
            wave.wave_status = status;
        })
    }

    /**
     * Remove a POS from the wave
     * @param appleId
     */
    $scope.removePos = function(appleId){
        $http.delete('cpm-pos/delete-pos-from-wave/' + $scope.item._pk_wave, {
            data: [appleId]
        }).success(function(){
            var toDelete = _.findWhere($scope.item.pos, {pos_apple_id: appleId});
            $scope.item.pos.splice($scope.item.pos.indexOf(toDelete), 1);
        })
    }
}