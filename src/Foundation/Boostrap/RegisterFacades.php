<?php

namespace Adepto\Foundation\Boostrap;

use Adepto\Foundation\Application;

// change this to a service provider or something like that
class RegisterFacades
{
    private array $facades = [
        \Adepto\Facades\Router::class => \Adepto\Router::class,
        \Adepto\Facades\View::class => \Adepto\View::class,
    ];

    public function boostrap(Application $app)
    {
        foreach ($this->facades as $facade => $concrete) {
            $facade::setApplication($app);

            // not sure if all facades should be singletons
            // not even sure if Router should be singleton
            $app->singleton($facade::getFacadeAccessor(), function () use ($concrete) {
                return new $concrete;
            });
        }
    }
}
