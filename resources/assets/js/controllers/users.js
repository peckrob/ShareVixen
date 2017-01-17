angular.module('sharevixen')
    .controller('users', function ($scope, $stateParams, $state, $timeout, User) {
        $scope.users = [];

        $scope.save = function(user) {
            console.log(user);
        };

        $scope.destroy = function(user) {
            user.$delete(null, function() {
                $scope.loadInitial();
            });
        }

        $scope.loadInitial = function() {
            User.query().$promise.then(function(data) {
                $scope.users = data;
            });
        }

        $scope.loadInitial();
    });
