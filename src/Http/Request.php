<?php

namespace Adepto\Http;

class Request
{
    public function __construct(
        protected string $root,
        protected string $uri,
        protected string $path,
        protected string $method,
        public array $cookies,
        public array $query,
        public array $headers,
    ) {
        //
    }

    public static function capture(): Request
    {
        $cookies = $_COOKIE;
        $queryParameters = $_GET;
        $headers = [
            'host' => $_SERVER['HTTP_HOST'],
            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
            'accept' => $_SERVER['HTTP_ACCEPT'],
            'accept-language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            'accept-encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'],
            'connection' => $_SERVER['HTTP_CONNECTION'],
            'cookie' => $_SERVER['HTTP_COOKIE'],
            'upgrade-insecure-requests' => $_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS'],
            'sec-fetch-dest' => $_SERVER['HTTP_SEC_FETCH_DEST'],
            'sec-fetch-mode' => $_SERVER['HTTP_SEC_FETCH_MODE'],
            'sec-fetch-site' => $_SERVER['HTTP_SEC_FETCH_SITE'],
            'priority' => $_SERVER['HTTP_PRIORITY'],
        ];

        $request =  new Request(
            root: "{$_SERVER['DOCUMENT_ROOT']}/../",
            method: "{$_SERVER['REQUEST_METHOD']}",
            uri: "{$_SERVER['REQUEST_URI']}",
            path: "{$_SERVER['PATH_INFO']}",
            cookies: $cookies,
            query: $queryParameters,
            headers: $headers,
        );

        return $request;
    }

    public function cookie(string $key)
    {
        return $this->cookies[$key];
    }

    public function query(string $key)
    {
        return isset($this->query[$key]) ? $this->query[$key] : '';
    }

    public function all()
    {
        return $this->query;
    }

    public function header(string $key)
    {
        return $this->headers[$key];
    }

    public function root()
    {
        return $this->headers['host'];
    }

    public function getUri()
    {
        return $this->root() . $this->uri;
    }

    public function path()
    {
        return $this->path ? $this->path : "/";
    }

    public function method()
    {
        return $this->method;
    }
}
