function ctrlListWave($scope, $http, $routeParams, $injector, $filter){

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
            _ke_activity: id
        });
    }

    /**
     * Remove an activity
     */
    $scope.removeActivity = function(){

    }
}