(function () {
    'use strict';
    angular.module('project-mgr')
        .controller('AuthController', function ($auth, $scope, $rootScope, $location, $window) {

            $scope.githubLogin = function () {
                $auth.authenticate('github')
                    .then(function (response) {
                        $window.localStorage.currentUser = JSON.stringify(response.data.user);
                        $rootScope.currentUser = JSON.parse($window.localStorage.currentUser);
                        $rootScope.isLoggedIn = true;
                        $location.path('/');
                    })
                    .catch(function (response) {
                        console.log(response.data);
                    });
            };

            $scope.emailLogin = function () {
                $auth.login({email: $scope.email, password: $scope.password})
                    .then(function (response) {
                        $window.localStorage.currentUser = JSON.stringify(response.data.user);
                        $rootScope.currentUser = JSON.parse($window.localStorage.currentUser);
                        $rootScope.isLoggedIn = true;
                        $location.path('/');
                    })
                    .catch(function (response) {
                        console.log(response);
                        $scope.errorMessage = {};
                        if (angular.isArray(response.data.message)) {
                            angular.forEach(response.data.message, function (message, field) {
                                $scope.loginForm[field].$setValidity('server', false);
                                $scope.errorMessage[field] = response.data.message[field];
                            });
                        } else {
                            //$scope.loginForm['failedLogin'].$setValidity('server', false);
                            $scope.errorMessage['failedLogin'] = response.data.message;
                        }

                    });
            };
        });

})();
