angular.module('openITCOCKPIT').directive('nagiosModuleConfig', function($http){
    return {
        restrict: 'E',
        templateUrl: '/ConfigurationFiles/NagiosModuleConfig.html',
        scope: {},

        controller: function($scope){

            $scope.post = {};

            $scope.init = true;
            $scope.load = function(){
                $http.get('/ConfigurationFiles/NagiosModuleConfig.json', {
                    params: {
                        'angular': true
                    }
                }).then(function(result){
                    $scope.post = result.data.config;
                    $scope.init = false;
                }, function errorCallback(result){
                    if(result.status === 404){
                        window.location.href = '/angular/not_found';
                    }
                });
            };

            $scope.submit = function(){
                $http.post('/ConfigurationFiles/NagiosModuleConfig.json?angular=true',
                    $scope.post
                ).then(function(result){
                    console.log('Data saved successfully');
                    window.location.href = '/ConfigurationFiles/index';
                }, function errorCallback(result){
                    if(result.data.hasOwnProperty('error')){
                        $scope.errors = result.data.error;
                    }
                });
            };

            $scope.load();

        }

    };
});