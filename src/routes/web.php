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


/** @var $router Laravel\Lumen\Routing\Router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1'], function () use ($router) {

    $router->group(['prefix' => 'chat', 'middleware' => 'auth'], function () use ($router) {

        $router->get('receive/{chat_room_id}[/{timestamp}]',  'ChatController@receive');
        $router->post('send', 'ChatController@send');

    });


    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('create', 'UserController@create');
        $router->post('login', 'UserController@login');
    });

});

$router->get('/{any:.*}', function() use ($router) {
    return response()->json(['error' => true, 'message' => 'route not found or method not allowed'], 404);
});
