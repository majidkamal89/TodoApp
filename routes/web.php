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

$router->get('/', 'HomeController@index');

$router->group(['prefix' => 'api'], function () use ($router){

    $router->group(['namespace' => 'Auth'], function() use ($router) {
        $router->post('/register', 'AuthController@register');
        $router->post('/login', 'AuthController@login');
    });

    $router->group(['middleware' => 'auth'], function() use ($router) {
        $router->group(['namespace' => 'Auth'], function() use ($router) {
            $router->post('/logout', 'AuthController@logout');
        });
        // Todos routes
        $router->group(['prefix' => 'todo'], function() use ($router) {
            $router->get('/list', 'TodoController@index');
            $router->put('/store', 'TodoController@store');
            $router->put('/update', 'TodoController@update');
            $router->delete('/delete/{id}', 'TodoController@destroy');
        });
        // Category routes
        $router->group(['prefix' => 'category'], function() use ($router) {
            $router->get('/list', 'CategoryController@index');
            $router->delete('/delete/{id}', 'CategoryController@destroy');
            $router->put('/store', 'CategoryController@store');
            $router->put('/update', 'CategoryController@update');
        });

    });
});
