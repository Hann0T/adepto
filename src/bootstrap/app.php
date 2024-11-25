<?php

use Adepto\Facades\Router;
use Adepto\Foundation\Application;
use Adepto\Http\Request;

function bootstrapApp()
{
    try {
        $app = Application::getInstance();
        $app->bootstrap();

        $request = Request::capture();
        $app->handleRequest($request);

        // other way to load the web.php?
        // https://github.com/laravel/framework/blob/11.x/src/Illuminate/Foundation/Configuration/ApplicationBuilder.php#L150
        include_once __DIR__ . '/../routes/web.php';

        $callback = Router::resolve($request);
        echo $callback();
    } catch (\Throwable $e) {
        dd($e);
        echo 505;
    }
}
