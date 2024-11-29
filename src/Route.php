<?php

namespace Adepto;

use Adepto\Http\Request;

class Route
{
    public function __construct(
        public string $method,
        public string $uri,
        public mixed $action
    ) {
        //
    }

    public function hasParameter(): bool
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

    public function getUriParameters(Request $request): array
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

    public function run(Request $request): mixed
    {
        if ($this->hasParameter()) {
            $params = $this->getUriParameters($request);
        }

        if (!($this->action instanceof \Closure)) {
            return $this->runController($params ?? []);
        }

        $action = $this->action;
        if ($params) {
            return $action(...$params);
        }

        return $action();
    }

    public function runController(array $params = [])
    {
        if (!is_array($this->action)) {
            throw new \Error("Invalid route action: [{$this->action}].");
        }

        if (!isset($this->action[0])) {
            throw new \Error("Route action is invalid.");
        }

        $class = $this->action[0];
        if (!class_exists($class)) {
            throw new \Error("Target class [{$class}] does not exist.");
        }

        $instance = app()->make($class);

        if (!isset($this->action[1])) {
            throw new \Error("Class [{$class}] invalid method.");
        }

        $method = $this->action[1];
        if (!method_exists($instance, $method)) {
            throw new \Error("Call to undefined method [{$class}::{$method}()].");
        }

        return call_user_func([$instance, $method], ...$params);
    }
}
