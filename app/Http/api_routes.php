<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/**
 * @var \Dingo\Api\Routing\Router $api
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Acme\Api\v1'], function ($api) {

    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('/', ['uses' => 'AuthController@login']);
        $api->post('/signup', ['uses' => 'AuthController@create']);
    });

    $api->group(['prefix' => 'projects'], function ($api) {
        $api->get('/', ['uses' => 'ProjectsController@index']);

    });
});