<?php

namespace Adepto\Support;

use Adepto\Foundation\Application;

class FacadeServiceProvider implements ServiceProvider
{
    private array $singletons = [
        \Adepto\Facades\Router::class => \Adepto\Http\Router::class,
        \Adepto\Facades\View::class => \Adepto\View::class,
    ];

    public function boot(Application $app): void
    {
        foreach ($this->singletons as $facade => $concrete) {
            $facade::setApplication($app);

            $app->singleton($facade::getFacadeAccessor(), function () use ($concrete) {
                return new $concrete;
            });
        }
    }
}
