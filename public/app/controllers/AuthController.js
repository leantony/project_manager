(function () {

    'use strict';

    angular
        .module('project_mgr')
        .controller('AuthController', AuthController);


    function AuthController($auth, $state) {

        var a = this;

        a.login = function () {

            var credentials = {
                email: a.email,
                password: a.password
            };

            $auth.login(credentials).then(function (data) {

                $state.go('projects', {});
            });
        }

    }

})();