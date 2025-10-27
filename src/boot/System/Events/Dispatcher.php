<?php

namespace Boot\System\Events;

use Illuminate\Events\Dispatcher as BaseDispatcher;
use Boot\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Boot\Contracts\Broadcasting\Factory as BroadcastFactory;

class Dispatcher extends BaseDispatcher implements DispatcherContract
{

    /**
     * Determine if the payload has a broadcastable event.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function shouldBroadcast(array $payload)
    {
        return isset($payload[0]) &&
               $payload[0] instanceof ShouldBroadcast &&
               $this->broadcastWhen($payload[0]);
    }

    /**
     * Broadcast the given event class.
     *
     * @param  \Boot\Contracts\Broadcasting\ShouldBroadcast  $event
     * @return void
     */
    protected function broadcastEvent($event)
    {
        $this->container->make(BroadcastFactory::class)->queue($event);
    }

}
