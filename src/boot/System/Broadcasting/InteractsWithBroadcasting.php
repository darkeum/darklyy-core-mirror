<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Broadcasting;

use Illuminate\Support\Arr;

use function Illuminate\Support\enum_value;

trait InteractsWithBroadcasting
{
    /**
     * The broadcaster connection to use to broadcast the event.
     *
     * @var array
     */
    protected $broadcastConnection = [null];

    /**
     * Broadcast the event using a specific broadcaster.
     *
     * @param  \UnitEnum|array|string|null  $connection
     * @return $this
     */
    public function broadcastVia($connection = null)
    {
        $connection = enum_value($connection);

        $this->broadcastConnection = is_null($connection)
            ? [null]
            : Arr::wrap($connection);

        return $this;
    }

    /**
     * Get the broadcaster connections the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastConnections()
    {
        return $this->broadcastConnection;
    }
}
