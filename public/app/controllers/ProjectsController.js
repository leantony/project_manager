(function() {

    'use strict';

    angular
        .module('project_mgr')
        .controller('ProjectsController', ProjectsController);

    function ProjectsController($http) {

        var a = this;

        a.projects;
        a.error;

        a.getUsers = function() {

            // This request will hit the index method in the AuthenticateController
            // on the Laravel side and will return the list of users
            $http.get('user/projects').success(function(projects) {
                a.projects = projects;
            }).error(function(error) {
                a.error = error;
            });
        }
    }

})();