<?php

use Adepto\Facades\Router;
use Adepto\Foundation\Application;
use Adepto\Http\Exceptions\RouteNotFoundException;
use Adepto\Http\Request;

function bootstrapApp()
{
    $app = Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: dirname(__DIR__) . '/../routes/web.php'
        )
        ->create();

    try {
        $request = Request::capture();
        $app->handleRequest($request);

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
