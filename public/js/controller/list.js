function ctrlList($scope, $http, $modal, $q){

    /**
     * Init list of items
     * @type {Array}
     */
    $scope.list = null;

    /**
     * Define configuration object defaults
     * @type {void|*|Object}
     */
    $scope.config = angular.extend({
        type: 'wave',
        fieldName: 'wave_name',
        title: 'Wave'
    }, $scope.config);
    $scope.config.key = '_pk_' + $scope.config.type;

    /**
     * Edit Item
     * @param item
     */
    $scope.edit = function(item){
        $scope.originalItem = item;

        var request = $scope.loadingPromise(item).then(function(item){
            item = item ? item:{};
            //Set default values
            $scope.setDefaultValues(item);
            //Store original item
            $scope.item = angular.copy(item);
            $scope.modalDetail = $modal({
                template: 'template/'+$scope.config.type+'/detail.html',
                title: item && item[$scope.config.fieldName] ? $scope.config.title + ': ' + item[$scope.config.fieldName]:'New ' + $scope.config.title,
                scope: $scope
            });
        })

        $scope.tracker.addPromise(request);
    }

    /**
     * Save Item
     * @param item
     * @param callback
     */
    $scope.save = function(item, callback){
        //Compute id
        var id = item && item[$scope.config.key] ? item[$scope.config.key]:'';
        //Update/Insert
        $http({
            url: $scope.config.type + '/' + id,
            data: item,
            method: id ? 'PUT':'POST'
        }).success(function(data){
            if($scope.originalItem)
            {
                angular.extend($scope.originalItem, data);
            }
            else
            {
                //Else add to list
                angular.extend(item, data);
                $scope.originalItem = angular.copy(item);
                $scope.list.push($scope.originalItem)
            }

            if(callback)
            {
                callback();
            }
        });
    }

    /**
     * Delete an item from the list
     * @param item
     */
    $scope.delete = function(item){
        if(confirm('Are you sure you want to delete this ' + $scope.config.title + '?'))
        {
            var request = $http.delete($scope.config.type + '/' + item[$scope.config.key]).success(function(){
                $scope.list.splice($scope.list.indexOf(item), 1);
            });
            $scope.tracker.addPromise(request);
        }
    }

    /**
     * Extendable function to set default values when saving item
     * @param item
     * @returns {*}
     */
    $scope.setDefaultValues = function(item){
        return item;
    }

    /**
     * Overwrite this scope method if you want a specific loading before displaying the detail modal
     * @param item
     * @returns {promise}
     */
    $scope.loadingPromise = function(item){
        var deferred = $q.defer();
        deferred.resolve(item)
        return deferred.promise;
    }
}