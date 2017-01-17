angular
    .module('sharevixen')
    .factory('Files', function($resource) {
        // ngResource call to our static data
        var Files = $resource("/srv/files/:id", {id: "@id"}, {});

        return Files;
    });
