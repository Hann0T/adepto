<?php

namespace Adepto\Facades;

/**
 * @method static string render(string $view, array $params = [])
 */
class View extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'view';
    }
}
