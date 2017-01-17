angular.module('sharevixen').config(function(treeConfig) {
    treeConfig.defaultCollapsed = true; // collapse nodes by default
    treeConfig.appendChildOnHover = true; // append dragged nodes as children by default
    treeConfig.emptryTreeClass = "";
});

angular.module('sharevixen')
	.filter('filesize', function () {
        console.log("foo");
		return function (size) {
			if (isNaN(size))
				size = 0;

			if (size < 1024)
				return size + ' Bytes';

			size /= 1024;

			if (size < 1024)
				return size.toFixed(2) + ' Kb';

			size /= 1024;

			if (size < 1024)
				return size.toFixed(2) + ' Mb';

			size /= 1024;

			if (size < 1024)
				return size.toFixed(2) + ' Gb';

			size /= 1024;

			return size.toFixed(2) + ' Tb';
		};
	});

angular.module('sharevixen')
    .controller('files', function ($scope, $stateParams, $state, $timeout, Files) {
        $scope.files = [];
        $scope.query = "";
        $scope.all_files = [];

        $scope.loadChildren = function(node, itemScope) {
            var indentLevelAdd = node.path.split("/").length;

            if (node.nodes.length == 0) {
                itemScope.loading = true;
                Files.query({"l": 1, "p": node.path}).$promise.then(function(data) {
                    data.forEach(function(element, index, array) {
                        data[index]["indent_level"] += indentLevelAdd;
                    });

                    node.nodes = data;
                    itemScope.toggle(itemScope);
                    itemScope.loading = false;
                });

            } else {
                itemScope.toggle(itemScope);
            }
        };

        $scope.search = function() {
            if ($scope.query.length > 0) {
                $timeout(function() {
                    Files.query({"q": $scope.query}).$promise.then(function(data) {
                        $scope.all_files = data;
                        $scope.files = $scope.all_files;

                        if ($scope.query.length > 0) {
                            $timeout(function() {
                                $scope.$broadcast('angular-ui-tree:expand-all');
                            }, 500);
                        }
                    });
                }, 100);
            } else {
                $scope.loadInitial();
            }
        };

        $scope.collapseAll = function () {
            $scope.$broadcast('angular-ui-tree:collapse-all');
          };

        $scope.visible = function (item) {
            return !($scope.query && $scope.query.length > 0
                && item.title.indexOf($scope.query) == -1);
        };

        $scope.loadInitial = function() {
            $scope.query = "";
            Files.query({"l": 1}).$promise.then(function(data) {
                $scope.all_files = data;
                $scope.files = $scope.all_files;
            });
        }

        $scope.loadInitial();
    });
