<?php

namespace Boot\System\Dashboard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Boot\System\Dashboard\Dashboard
 */
class Dashboard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dashboard';
    }
}
