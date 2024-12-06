<?php

use Adepto\Facades\View;
use Adepto\Foundation\Application;

if (!function_exists('dd')) {
    function dd(...$args)
    {
        echo '<pre style="background: #f4f4f4; border: 1px solid #ddd; border-left: 3px solid #f36d33; color: #666; page-break-inside: avoid; font-family: monospace; font-size: 15px; line-height: 1.6; margin-bottom: 1.6em; max-width: 100%; overflow: auto; padding: 1em 1.5em; display: block; word-wrap: break-word;">';
        call_user_func_array('var_dump', $args);
        echo '</pre>';
        die();
    }
}

if (!function_exists('view')) {
    function view(string $view, array $params = []): string
    {
        return View::render($view, $params);
    }
}

if (!function_exists('app')) {
    function app(string $abstract = '', array $params = [])
    {
        if ($abstract) {
            return Application::getInstance()->make($abstract, $params);
        }

        return Application::getInstance();
    }
}

if (!function_exists('request')) {
    function request(string $key = '')
    {
        $request = Application::getInstance()->make('request');

        if ($key) {
            return $request->query($key);
        }

        return $request;
    }
}

if (!function_exists('response')) {
    function response(string $content = '', int $status = 200, array $headers = []): \Adepto\Http\Response
    {
        return new \Adepto\Http\Response($content ?? '', $status, $headers);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $status = 301, array $headers = []): \Adepto\Http\Response
    {
        return (new \Adepto\Http\Response)->redirect($path, $status, $headers);
    }
}

if (!function_exists('abort')) {
    function abort(int $status = 400, string $message = '', array $headers = []): \Adepto\Http\Response
    {
        return (new \Adepto\Http\Response)->abort($status, $message, $headers);
    }
}
