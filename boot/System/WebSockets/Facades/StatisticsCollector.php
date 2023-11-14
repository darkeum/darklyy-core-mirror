<?php

namespace Boot\System\WebSockets\Facades;

use Boot\System\WebSockets\Contracts\StatisticsCollector as StatisticsCollectorInterface;
use Illuminate\Support\Facades\Facade;

class StatisticsCollector extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return StatisticsCollectorInterface::class;
    }
}
