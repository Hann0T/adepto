<?php

namespace Adepto\Http;

use Adepto\Bus\Stack;
use Adepto\Http\Exceptions\RouteNotFoundException;
use Adepto\Http\Request;
use Adepto\Http\Response;

class TreeNode
{
    public mixed $value;
    public ?Route $route = null;
    public array $children = [];

    public function __construct(mixed $value, Route $route = null)
    {
        $this->value = $value;
        $this->route = $route;
    }

    public function insert(string $uri, Route $route)
    {
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);
        if (empty($uri)) {
            return;
        }

        if ($this->value == $uri[0]) {
            return;
        }

        $curr = $this;
        foreach ($uri as $segment) {
            if (count($curr->children) <= 0) {
                $node = new TreeNode($segment, $route);
                $curr->children[$segment] = $node;
                $curr = $node;
                continue;
            }

            if (!isset($curr->children[$segment])) {
                $node = new TreeNode($segment, $route);
                $curr->children[$segment] = $node;
                $curr = $node;
                continue;
            }

            $curr = $curr->children[$segment];
        }
    }

    public function searchRoute(string $path): ?Route
    {
        $path = trim($path, '/');
        $path = explode('/', $path);
        if (empty($path)) {
            return null;
        }

        $curr = $this;
        foreach ($path as $segment) {
            $node = $curr->children[$segment];
            if ($node) {
                $curr = $node;
                continue;
            }

            $filtered = array_filter(
                $curr->children,
                fn ($node) => $node->value[0] == '{' && $node->value[strlen($node->value) - 1] == '}'
            );
            if (empty($filtered)) {
                return null;
            }

            $curr = array_values($filtered)[0];
        }

        return $curr->route;
    }
}

class Router
{
    private ?TreeNode $routes = null;

    public function __construct()
    {
        $this->routes = new TreeNode('root');
    }

    /**
     * Find the first route matching a given request.
     */
    public function matchRoute(Request $request): Route
    {
        $route = $this->routes->searchRoute($request->path());

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
        if ($uri == '/') {
            $this->routes->route = $route;
        } else {
            $this->routes->insert($uri, $route);
        }
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
}
