<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('/', ['uses' => 'App\Http\Controllers\Auth\AuthController@LoginViaApi']);
    });
});

Route::get('/', function () {
    return view('welcome');
});

//Route::group(['prefix' => 'auth/login'], function () {
//    get('/', ['as' => 'auth.get.login', 'uses' => 'Auth\AuthController@getLogin']);
//    post('/verify', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@LoginViaApi']);
//});
//
//Route::group(['prefix' => 'user'], function(){
//    get('projects', ['as' => 'user.projects', 'uses' => 'projects\ProjectsController@index']);
//});


//Route::any('{path?}', function(){
//    return File::get(public_path() . '/views/index.html');
//})->where("path", ".+");
