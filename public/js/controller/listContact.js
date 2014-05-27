function ctrlListContact($scope, $http, $injector){

    //Config
    $scope.config = {
        type: 'contact',
        fieldName: 'contact_name',
        title: 'Contact'
    }

    //Call parent controller
    $injector.invoke(ctrlList, this, {$scope: $scope});

    $scope.contacts = [];
    $scope.$watch(function(){return $scope.dictionary}, function(){
        if($scope.dictionary.countries)
        {
            //Prefill object
            $scope.dictionary.countries.forEach(function(country){
                $scope.contacts.push({
                    contact_country: country.id,
                    country_name: country.name
                });
            });

            //Launch request for real values
            var request = $http.get('contact').success(function(data){
                data.forEach(function(item){
                    var contact = _.find($scope.contacts, function(value){return value.contact_country == item.contact_country})
                    if(contact)
                    {
                        angular.extend(contact, item);
                    }
                })
            });
            $scope.tracker.addPromise(request);
        }
    })

}