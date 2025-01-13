<?php

namespace Adepto\Facades;

/**
 * @method static void dispatch(\Adepto\Bus\Contracts\Queue\ShouldQueue $job)
 */
class Bus extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'bus';
    }
}
