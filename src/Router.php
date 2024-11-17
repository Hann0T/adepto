<?php

namespace Adepto;

enum HTTPMethods
{
    case GET;
    case POST;
    case UPDATE;
    case DELETE;
}

class Router
{
    private static $instance = null;

    public $routes = [];

    public function __construct()
    {
        foreach (HTTPMethods::cases() as $method) {
            $this->routes[$method->name] = [];
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    public function resolve($request)
    {
        $method = $request['REQUEST_METHOD'];
        $uri = $request['REQUEST_URI'];
        return $this->routes[$method][$uri];
    }

    public function get(string $route, callable $callback)
    {
        $this->routes['GET'][$route] = $callback;
    }

    public function post(string $route, callable $callback)
    {
        $this->routes['POST'][$route] = $callback;
    }
}
