<?php

namespace Adepto\Http;

use Adepto\Bus\Stack;
use Adepto\Http\Exceptions\RouteNotFoundException;
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
            throw new RouteNotFoundException("404 Route does not exists.");
        }

        return $route;
    }

    public function resolve(Request $request): Response
    {
        $route = $this->matchRoute($request);
        $middlewares = $route->middlewares();

        $next = fn ($request) => $route?->run($request);
        $response = $this->runMiddlewares($request, $middlewares, $next);
        return $response;
    }

    protected function runMiddlewares(Request $request, Stack $middlewares, mixed $next): Response
    {
        if ($middlewares->len <= 0) {
            return $next($request);
        }

        //$request->setHeader('redirect', 'true');
        $pipeline = $next;
        while ($middlewares->len > 0) {
            $middleware = app()->make($middlewares->pop());
            $pipeline = function ($request) use ($middleware, $pipeline) {
                return $middleware->handle($request, $pipeline);
            };
        }

        return $pipeline($request);
    }

    public function createRoute(string $method, string $uri, mixed $action): Route
    {
        return new Route($method, $uri, $action);
    }

    public function addRoute(string $method, string $uri, mixed $action): Route
    {
        $uri = '/' . trim($uri, '/');
        $route = $this->createRoute($method, $uri, $action);
        $this->routes[] = $route;
        return $route;
    }

    public function get(string $route, mixed $callback): Route
    {
        return $this->addRoute('GET', $route, $callback);
    }

    public function post(string $route, mixed $callback): Route
    {
        return $this->addRoute('POST', $route, $callback);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
