<?php

namespace Boot\System\Core\Abstracts\Events\Providers;

use Boot\System\Core\Abstracts\Events\Dispatcher\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Boot\System\Events\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    public function register()
    {
        $this->app->singleton('events', function ($app) {
            return (new Dispatcher($app))->setQueueResolver(function () use ($app) {
                return $app->make(QueueFactoryContract::class);
            });
        });
    }
}
