<?php

namespace Adepto\Facades;

use Adepto\Foundation\Application;

class Facade
{
    protected static Application $app;

    public static function __callStatic($name, $arguments)
    {
        $instance = static::resolveInstance(static::getFacadeAccessor());
        if (method_exists($instance, $name)) {
            return call_user_func([$instance, $name], ...$arguments);
        }

        throw new \Error("Method {$name} does not exists.");
    }

    public static function setApplication(Application $app)
    {
        static::$app = $app;
    }

    public static function getApplicationInstance(): Application
    {
        return static::$app;
    }

    public static function resolveInstance(string $abstract)
    {
        return static::$app->make($abstract);
    }

    public static function getFacadeAccessor()
    {
        throw new \Error('Facade does not implement getFacadeAccessor method.');
    }
}
