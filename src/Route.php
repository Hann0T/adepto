<?php

namespace Adepto;

class Route
{
    public function __construct(
        public $method,
        public $uri,
        public $action
    ) {
    }

    public function hasParameter(): bool
    {
        return preg_match("/\{([a-zA-Z]+)\}/", $this->uri);
    }

    public function matches($request): bool
    {
        $method = $request['REQUEST_METHOD'];
        $uri = $request['REQUEST_URI'];

        if ($this->method !== $method) {
            return false;
        }

        if ($this->hasParameter()) {
            $str = preg_replace("/\{([a-zA-Z]+)\}/", "", $this->uri);
            return str_contains($uri, $str);
        }

        return $this->uri === $uri;
    }

    public function bindParameter($request)
    {
        $str = preg_replace("/\{([a-zA-Z]+)\}/", "", $this->uri);
        $uri = $request['REQUEST_URI'];
        $str = str_replace($str, '', $uri);
        $action = $this->action;
        $this->action = fn () => $action($str);
    }
}
