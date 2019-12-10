<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Generate random string
$router->get('appKey', function () {
    return \Illuminate\Support\Str::random(32);
});

$router->group(['prefix' => 'api'], function ($router) {
    $router->post('product', 'ProductController@store');
    $router->get('product/{id:[0-9]+}', 'ProductController@show');
    $router->put('product/{id:[0-9]+}', 'ProductController@update');
    $router->delete('product/{id:[0-9]+}', 'ProductController@destroy');
    $router->get('product', 'ProductController@index');
});
