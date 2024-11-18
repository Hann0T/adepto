<?php

use Adepto\Router;

function bootstrapApp()
{
    $router = Router::getInstance();

    $router->get('/hello', function () {
        return 'world';
    });

    $router->get('/hello2', function () {
        return 'world 2';
    });

    $router->get('/user/{id}', function ($id) {
        return 'user with ID: ' . $id;
    });

    $router->get('/user/{id}/posts', function ($id) {
        return 'user with ID: ' . $id . ' With Posts';
    });

    $router->post('/users', function () {
        return json_encode(['user' => ['name' => 'num 1']], true);
    });

    try {
        $callback = $router->resolve($_SERVER);
        echo $callback();
    } catch (\Throwable $e) {
        printf($e);
        echo 505;
    }
}
