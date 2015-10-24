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

Route::group(['prefix' => 'auth/login'], function () {
    get('/', ['as' => 'auth.get.login', 'uses' => 'Auth\AuthController@getLogin']);
    post('/verify', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@LoginViaApi']);
});

// OAUTH
Route::group(['prefix' => 'auth/oauth2'], function () {

    // API login
    get('/', ['as' => 'auth.loginUsingAPI', 'uses' => 'Auth\AuthController@apiAuth', 'middleware' => 'api.verify']);

    // account creation. Requires that a valid user was returned by the API
    get('/register', ['as' => 'auth.fill', 'uses' => 'Auth\AuthController@getMiniRegistrationForm', 'middleware' => 'user.found']);
    post('/register', ['as' => 'auth.fill.post', 'uses' => 'Auth\AuthController@createAccountViaOAUTHData', 'middleware' => 'user.found']);

    // handle user verification via OAUTH
    get('/callback', ['as' => 'auth.getDataFromAPI', 'uses' => 'Auth\AuthController@handleOAUTHCallback']);

});

Route::group(['prefix' => 'user'], function(){
    get('projects', ['as' => 'user.projects', 'uses' => 'projects\ProjectsController@index']);
});


//Route::any('{path?}', function(){
//    return File::get(public_path() . '/views/index.html');
//})->where("path", ".+");
