<?php

use Adepto\Facades\Router;
use Adepto\Foundation\Application;
use Adepto\Http\Exceptions\RouteNotFoundException;
use Adepto\Http\Request;

function bootstrapApp()
{
    $app = Application::getInstance();
    $app->bootstrap();
    try {
        $request = Request::capture();
        $app->handleRequest($request);

        // other way to load the web.php?
        // https://github.com/laravel/framework/blob/11.x/src/Illuminate/Foundation/Configuration/ApplicationBuilder.php#L150
        include_once __DIR__ . '/../routes/web.php';

        $response = Router::resolve($request)->prepare();
        $app->terminate($response);
    } catch (RouteNotFoundException $e) {
        $response = view('404', [
            'message' => $e->getMessage()
        ])->setStatusCode(404);
        $app->terminate($response);
    } catch (\Throwable $e) {
        $response = view('500', [
            'message' => $e->getMessage()
        ])->setStatusCode(500);
        $app->terminate($response);
    }
}
