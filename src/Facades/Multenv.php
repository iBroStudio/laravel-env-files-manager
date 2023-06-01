<?php

namespace IBroStudio\Multenv\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static decrypt()
 * @method static encrypt()
 * @method static key()
 * @method static merge()
 */
class Multenv extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \IBroStudio\Multenv\Multenv::class;
    }
}
