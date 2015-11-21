(function () {
    'use strict';
    angular.module('project-mgr')
        .controller('HomeController', function ($scope, $auth, $window) {

            $scope.isAuthenticated = function () {
                return $auth.isAuthenticated();
            };

            $scope.logout = function() {
                $auth.logout();
                delete $window.localStorage.currentUser;
            };

            $scope.linkGithub = function () {
                // connect email account with github
                $auth.link('github')
                    .then(function (response) {
                        $window.localStorage.currentUser = JSON.stringify(response.data.user);
                        $rootScope.currentUser = JSON.parse($window.localStorage.currentUser);
                    });
            };

        });
})();
