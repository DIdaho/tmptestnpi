function ctrlListActivity($scope, $http, $injector){

    //Config
    $scope.config = {
        type: 'activity',
        fieldName: 'activ_title',
        title: 'Activity'
    }

    //Call parent controller
    $injector.invoke(ctrlList, this, {$scope: $scope});

    //Load the list
    $http.get('activity').success(function(data){
        $scope.list = data;
//        $scope.edit(data[0])
    });

    /**
     * Set default values when insert/update
     * @param item
     */
    $scope.setDefaultValues = function(item){
        if( ! item.activ_config)
        {
            item.activ_config = {};
        }
        if( ! item.activ_config.type)
        {
            item.activ_config.type = 'LIST';
        }
    }

    /**
     * Add answer to the selected activity of type LIST
     */
    $scope.addAnswer = function(){
        if( ! $scope.item.activ_config.answers)
        {
            $scope.item.activ_config.answers = [];
        }
        var count = $scope.item.activ_config.answers.length + 1;
        $scope.item.activ_config.answers.push({
            label: 'Label ' + count,
            value: count
        });
    }

    /**
     * Delete answer from activity of type LIST
     * @param answer
     */
    $scope.deleteAnswer = function(answer){
        $scope.item.activ_config.answers.splice($scope.item.activ_config.answers.indexOf(answer), 1);
    }
}