<?php

namespace Adepto\Facades;

/**
 * @method static void get(string $route, mixed $callback)
 * @method static void post(string $route, mixed $callback)
 * @method static \Adepto\Http\Response resolve(\Adepto\Http\Request $request)
 */
class Router extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'router';
    }
}
