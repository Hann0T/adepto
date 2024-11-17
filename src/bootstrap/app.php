<?php

use Adepto\Router;

function bootstrapApp()
{
    $router = Router::getInstance();

    $router->get('/hello', function () {
        return 'world';
    });

    $router->post('/users', function () {
        return json_encode(['user' => ['name' => 'num 1']], true);
    });

    try {
        $callback = $router->resolve($_SERVER);
        echo $callback();
    } catch (\Throwable) {
        echo 505;
    }
}
