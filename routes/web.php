<?php

use Adepto\AuthMiddleware;
use Adepto\Facades\Router;
use Adepto\Http\Request;
use Adepto\UserController;

Router::get('/', function () {
    return response()->redirect('/welcome');
});

Router::get('/welcome', function () {
    return view('home', ['name' => 'hans']);
})->middleware([AuthMiddleware::class]);

Router::get('/hello', function (Request $request) {
    return "{$request->method()} world";
});

Router::get('/login', function () {
    return 'login main page';
});

Router::get('/user/{id}', [UserController::class, 'get']);
Router::get('/users', [UserController::class, 'show']);
Router::post('/users', [UserController::class, 'post']);

Router::get('/user/{id}/posts', function ($id) {
    return "User with ID: {$id} and with Posts";
});

Router::get('/user/{id}/posts/{id}', function ($userId, Request $request, $postId) {
    return "User with ID: {$userId} and with Post with ID: {$postId} {$request->method()}";
});
