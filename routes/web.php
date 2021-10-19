<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\File;

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group([ 'prefix' => 'api'], function ($router) {
    $router->get('gallery', 'GalleryController@index');
    $router->post('gallery/{path}', 'GalleryController@update');
    $router->post('gallery/auth/{path}', 'GalleryController@updateAuth');
    $router->post('gallery', 'GalleryController@store');
    $router->get('gallery/{path}', 'GalleryController@show');
    $router->delete('gallery/{path}', 'GalleryController@delete');
    $router->delete('gallery/{gallery}/{image}', 'GalleryController@deleteImage');

    $router->get('images/{w}x{h}/{gallery}/{image}', 'ImageController@getImage');

    //facebook oauth
    $router->get('facebook/', 'FacebookController@invokeDialog');
    $router->get('callback/ajax/{query}', function () use ($router) {
        return view('view');
    });
    $router->get('callback/{query}', 'FacebookController@callback');
});
