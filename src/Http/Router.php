<?php

namespace Adepto\Http;

use Adepto\Http\Request;
use Adepto\Http\Response;

class Router
{
    private array $routes = [];

    /**
     * Find the first route matching a given request.
     */
    public function matchRoute(Request $request): Route
    {
        $filtered = array_filter($this->routes, function ($route) use ($request) {
            return $route->matches($request);
        });

        $route = array_values($filtered)[0];

        if (!$route) {
            throw new \Error("404 Route does not exists.");
        }

        return $route;
    }

    public function resolve(Request $request): Response
    {
        $response = $this->matchRoute($request)?->run($request);

        if ($response instanceof Response) {
            return $response;
        }

        return new Response(content: $response);
    }

    public function createRoute(string $method, string $uri, mixed $action): Route
    {
        return new Route($method, $uri, $action);
    }

    public function addRoute(string $method, string $uri, mixed $action): void
    {
        $uri = '/' . trim($uri, '/');
        $this->routes[] = $this->createRoute($method, $uri, $action);
    }

    public function get(string $route, mixed $callback): void
    {
        $this->addRoute('GET', $route, $callback);
    }

    public function post(string $route, mixed $callback): void
    {
        $this->addRoute('POST', $route, $callback);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
