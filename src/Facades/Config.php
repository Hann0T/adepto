<?php

namespace Adepto\Facades;

/**
 * @method public mixed get(string $key, string $default = '')
 * @method public bool has(string $key)
 */
class Config extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'config';
    }
}
