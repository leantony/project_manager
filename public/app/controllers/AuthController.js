(function () {

    'use strict';

    angular
        .module('project_mgr')
        .controller('AuthController', AuthController);


    function AuthController($auth, $state) {

        var vm = this;

        vm.login = function () {

            var credentials = {
                email: vm.email,
                password: vm.password
            };

            $auth.login(credentials).then(function (data) {

                $state.go('users', {});
            });
        }

    }

})();