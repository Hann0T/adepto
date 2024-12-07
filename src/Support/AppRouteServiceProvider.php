<?php

namespace Adepto\Support;

use Adepto\Foundation\Application;

class AppRouteServiceProvider implements ServiceProvider
{
    public function __construct(protected array $routes)
    {
        //
    }

    public function boot(Application $app): void
    {
        foreach ($this->routes as $route) {
            if ($route) {
                require_once $route;
            }
        }
    }
}
