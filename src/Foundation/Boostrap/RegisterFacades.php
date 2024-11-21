<?php

namespace Adepto\Foundation\Boostrap;

use Adepto\Foundation\Application;

class RegisterFacades
{
    private $facades = [
        \Adepto\Facades\Router::class => \Adepto\Router::class,
    ];

    public function boostrap(Application $app)
    {
        foreach ($this->facades as $facade => $concrete) {
            $facade::setApplication($app);
            $app->bind($facade::getFacadeAccessor(), function () use ($concrete) {
                return new $concrete;
            });
        }
    }
}
