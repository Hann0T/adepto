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
        $uri = '/' . trim($uri, '/');
        $uri = preg_replace('#/+#', '/', $uri);

        if ($this->method !== $method) {
            return false;
        }

        if ($this->hasParameter()) {
            // Split the registered uri by {parameters}.
            // We are splitting the registered URI to compare 
            // the length of its elements with the request URI.
            $registeredUri = preg_split("/\{([a-zA-Z]+)\}/", $this->uri, -1, PREG_SPLIT_DELIM_CAPTURE);
            $registeredUri = array_filter($registeredUri, fn ($v) => trim($v));

            // Split the request uri by '/[something]/'.
            // We are splitting the request URI to compare 
            // the length of its elements with the registered URI.
            $requestUri = preg_split('/(\/[^\/]+\/?|[^\/]+)/', $uri, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $requestUri = array_filter($requestUri, fn ($v) => trim($v));

            // If the two arrays have the same length,
            // we can conclude they are equal.
            // Therefore it's a perfect match.
            return count($registeredUri) == count($requestUri);
        }

        return $this->uri === $uri;
    }

    public function bindParameter($request)
    {
        $uri = $request['REQUEST_URI'];
        $uri = '/' . trim($uri, '/');
        $uri = preg_replace('#/+#', '/', $uri);

        // Split the request uri by '/[something]/'.
        $splitted = preg_split('/(\/[^\/]+\/?|[^\/]+)/', $uri, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // We are applying a filter to extract the arguments,
        // as these strings do not contain any '/'.
        $args = array_filter($splitted, fn ($v) => strpos($v, '/') === false);

        $action = $this->action;
        $this->action = fn () => $action(...$args);
    }
}
