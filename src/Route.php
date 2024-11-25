<?php

namespace Adepto;

use Adepto\Http\Request;

class Route
{
    public function __construct(
        public $method,
        public $uri,
        public $action
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

    public function bindParameter(Request $request)
    {
        $uri = $request->path();
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
