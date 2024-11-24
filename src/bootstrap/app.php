<?php

use Adepto\Facades\Router;
use Adepto\Foundation\Application;

function bootstrapApp()
{
    try {
        $app = new Application();
        $app->bootstrap();

        Router::get('/', function () {
            return view('home', ['name' => 'hans']);
        });

        Router::get('/hello', function () {
            return 'world';
        });

        Router::get('/hello2', function () {
            return 'world 2';
        });

        Router::get('/user/{id}', function ($id) {
            return 'user with ID: ' . $id;
        });

        Router::get('/user/{id}/posts', function ($id) {
            return 'user with ID: ' . $id . ' With Posts';
        });

        Router::get('/user/{id}/posts/{id}', function ($userId, $postId) {
            return 'user with ID: ' . $userId . ' With Post with ID: ' . $postId;
        });

        Router::post('/users', function () {
            return json_encode(['user' => ['name' => 'num 1']], true);
        });

        $callback = Router::resolve($_SERVER);
        echo $callback();
    } catch (\Throwable $e) {
        dd($e);
        echo 505;
    }
}
