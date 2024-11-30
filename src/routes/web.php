<?php

use Adepto\Facades\Router;
use Adepto\Http\Request;

Router::get('/', function () {
    return response()->redirect('/welcome');
});

Router::get('/welcome', function () {
    return view('home', ['name' => 'hans']);
});

Router::get('/hello', function (Request $request) {
    return 'world ' . $request->method();
});

Router::get('/hello2', function () {
    return 'world 2';
});

Router::get('/user/{id}', 'Adepto\UserController');
//Router::get('/user/{id}', [\Adepto\UserController::class, 'get']);
Router::get('/users', [\Adepto\UserController::class, 'show']);
Router::post('/users', [\Adepto\UserController::class, 'post']);

Router::get('/user/{id}/posts', function ($id) {
    return 'user with ID: ' . $id . ' With Posts';
});

Router::get('/user/{id}/posts/{id}', function ($userId, $postId) {
    return 'user with ID: ' . $userId . ' With Post with ID: ' . $postId;
});
