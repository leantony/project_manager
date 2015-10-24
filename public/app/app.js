(function() {

    'use strict';

    angular
        .module('project_mgr', ['ui.router', 'satellizer'])
        .config(function($stateProvider, $urlRouterProvider, $authProvider) {

            // Satellizer configuration that specifies which API
            // route the JWT should be retrieved from
            $authProvider.loginUrl = '/api/v1/auth';

            // Redirect to the auth state if any other states
            // are requested other than users
            $urlRouterProvider.otherwise('/auth');

            $stateProvider
                .state('auth', {
                    url: '/auth',
                    templateUrl: '../views/login.html',
                    controller: 'AuthController as auth'
                })
                .state('projects', {
                    url: '/projects',
                    templateUrl: '../views/projects.html',
                    controller: 'ProjectsController as projects'
                });
        });
})();