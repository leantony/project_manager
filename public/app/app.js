(function () {
    'use strict';
    angular.module('project-mgr', ['ngRoute', 'satellizer'])

        .config(['$routeProvider', '$authProvider', function ($routeProvider, $authProvider) {
            // Satellizer configuration that specifies which API
            // route the JWT should be retrieved from

            $authProvider.loginUrl = 'api/auth';
            $authProvider.signupUrl = 'api/auth/signup';
            $authProvider.github({
                url: '/auth/github',
                authorizationEndpoint: 'https://github.com/login/oauth/authorize',
                clientId: '13ff0c70b43670989095',
                redirectUri: window.location.origin,
                optionalUrlParams: ['scope'],
                scope: ['user:email'],
                scopeDelimiter: ' ',
                type: '2.0',
                popupOptions: {width: 1020, height: 618}
            });

            $routeProvider

                .when('/', {
                    templateUrl: 'views/home.html',
                    controller: 'HomeController'
                })
                .when('/login', {
                    templateUrl: 'views/login.html',
                    controller: 'AuthController'
                })
                .when('/signup', {
                    templateUrl: 'views/signup.html',
                    controller: 'SignUpController'
                })
                .when('/project/:id', {
                    templateUrl: 'views/project_detail.html',
                    controller: 'ProjectsController'
                })

                .otherwise({redirectTo: '/'});

        }]).run(function ($location, $auth, $window, $rootScope) {

            // register listener to watch route changes
            $rootScope.$on("$routeChangeStart", function (event, next, current) {


                //console.log($rootScope.isAuthenticated);
                if ($auth.isAuthenticated()) {
                    // if user is trying to access the login page, and they are already logged in
                    if (next.templateUrl === "views/login.html") {
                        $location.path("/");
                    }
                    if (next.templateUrl === "views/signup.html") {
                        $location.path("/");
                    }
                }

            });
        })
})();
