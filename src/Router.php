<?php

namespace Adepto;

class Router
{
    private static $instance = null;

    public array $routes = [];

    public function __construct()
    {
    }

    public static function getInstance(): Router
    {
        if (self::$instance == null) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    /**
     * Find the first route matching a given request.
     */
    public function matchRoute($request): Route | null
    {
        $filtered = array_filter($this->routes, function ($route) use ($request) {
            return $route->matches($request);
        });

        $route = array_values($filtered)[0];

        if ($route->hasParameter()) {
            $route->bindParameter($request);
        }

        return $route;
    }

    public function resolve($request): callable
    {
        return $this->matchRoute($request)->action;
    }

    public function createRoute(string $method, string $uri, callable $action): Route
    {
        return new Route($method, $uri, $action);
    }

    public function addRoute(string $method, string $uri, callable $action): void
    {
        $uri = '/' . trim($uri, '/');
        $this->routes[] = $this->createRoute($method, $uri, $action);
    }

    public function get(string $route, callable $callback): void
    {
        $this->addRoute('GET', $route, $callback);
    }

    public function post(string $route, callable $callback): void
    {
        $this->addRoute('POST', $route, $callback);
    }
}
