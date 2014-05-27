function ctrlList($scope, $http, $modal, $q){

    /**
     * Init list of items
     * @type {Array}
     */
    $scope.list = [];

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

        $scope.loadingPromise(item).then(function(item){
            item = item ? item:{};
            //Set default values
            $scope.setDefaultValues(item);
            //Store original item
            $scope.item = angular.copy(item);
            $scope.modalDetail = $modal({
                template: 'template/'+$scope.config.type+'/detail.html',
                title: item && item[$scope.config.fieldName] ? item[$scope.config.fieldName]:'New ' + $scope.config.title,
                scope: $scope
            });
        })
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
                $scope.list.push(data)
                angular.extend(item, data);
            }

            if(callback)
            {
                callback();
            }
        });
    }

    /**
     * Extendable function to set default values when saving item
     * @param item
     * @returns {*}
     */
    $scope.setDefaultValues = function(item){
        return item;
    }

    $scope.loadingPromise = function(item){
        var deferred = $q.defer();
        deferred.resolve(item)
        return deferred.promise;
    }
}