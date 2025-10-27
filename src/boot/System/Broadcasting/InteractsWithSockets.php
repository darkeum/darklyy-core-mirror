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

use Boot\Support\Facades\Broadcast;

trait InteractsWithSockets
{
    /**
     * The socket ID for the user that raised the event.
     *
     * @var string|null
     */
    public $socket;

    /**
     * Exclude the current user from receiving the broadcast.
     *
     * @return $this
     */
    public function dontBroadcastToCurrentUser()
    {
        $this->socket = Broadcast::socket();

        return $this;
    }

    /**
     * Broadcast the event to everyone.
     *
     * @return $this
     */
    public function broadcastToEveryone()
    {
        $this->socket = null;

        return $this;
    }
}
