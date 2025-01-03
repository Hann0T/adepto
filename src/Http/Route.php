<?php

namespace Adepto\Http;

use Adepto\Http\Request;

class Route
{
    protected array $middlewares = [];

    public function __construct(
        protected string $method,
        protected string $uri,
        protected mixed $action
    ) {
        //
    }

    protected function hasParameter(): bool
    {
        return preg_match("/\{([a-zA-Z]+)\}/", $this->uri);
    }

    public function matches(Request $request): bool
    {
        $method = $request->method();
        $uri = $request->path();

        $uri = '/' . trim($uri, '/');
        $uri = preg_replace('#/+#', '/', $uri);

        if ($this->method !== $method) {
            return false;
        }

        if ($this->hasParameter()) {
            // We make the pattern based on the registered uri
            // we are replacing the parameters in the uri with a regex
            $pattern = preg_replace("/\{([a-zA-Z]+)\}/", "[a-zA-Z0-9]+", $this->uri);
            $pattern = '#^' . $pattern . '$#';

            // We compare the pattern with the request uri
            $matches = preg_match($pattern, $uri);
            return $matches;
        }

        return $this->uri === $uri;
    }

    protected function getUriParameters(Request $request): array
    {
        $uri = $request->path();
        $uri = '/' . trim($uri, '/');
        $uri = preg_replace('#/+#', '/', $uri);

        // Split the request uri by '/[something]/'.
        $splitted = preg_split('/(\/[^\/]+\/?|[^\/]+)/', $uri, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // We are applying a filter to extract the arguments,
        // as these strings do not contain any '/'.
        $params = array_filter($splitted, fn ($v) => strpos($v, '/') === false);

        return $params;
    }

    public function run(Request $request): Response
    {
        if ($this->hasParameter()) {
            $params = $this->getUriParameters($request);
        }

        if ($this->action instanceof \Closure) {
            $response = app()->call($this->action, $params ?? []);
            if (!($response instanceof Response)) {
                $response = new Response(content: $response);
            }
            return $response;
        }

        if (is_array($this->action)) {
            $callable = $this->action;
        } else {
            $callable = explode("@", $this->action);
        }

        $this->validateActionArray($callable);
        $this->validateController($callable[0], $callable[1]);

        $response = app()->call($callable, $params ?? []);

        if (!($response instanceof Response)) {
            $response = new Response(content: $response);
        }

        return $response;
    }

    protected function validateController(string $class, string $method): void
    {
        if (!class_exists($class)) {
            throw new \Error("Target class [{$class}] does not exist.");
        }
        if (!method_exists($class, $method)) {
            throw new \Error("Call to undefined method [{$class}::{$method}()].");
        }
    }

    protected function validateActionArray(array $array): void
    {
        if (!isset($array[0]) || !isset($array[1])) {
            throw new \Error("Route action is not supported.");
        }
    }

    public function middlewares(): array
    {
        return $this->middlewares;
    }

    public function middleware(array|string $middleware): Route
    {
        if (is_array($middleware)) {
            foreach ($middleware as $m) {
                $this->middlewares[] = $m;
            }
            return $this;
        }

        $this->middlewares[] = $middleware;
        return $this;
    }
}
