<?php

namespace Adepto\Facades;

/**
 * @method static \Adepto\Http\Response render(string $view, array $params = [])
 */
class View extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'view';
    }
}
