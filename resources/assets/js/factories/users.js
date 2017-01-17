angular
    .module('sharevixen')
    .factory('User', function($resource) {
        // ngResource call to our static data
        var User = $resource("/srv/users/:id", {id: "@id"}, {
            "save": {
                method:'put'
            }
        });

        return User;
    });
