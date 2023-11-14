<?php

namespace Boot\System\Core\Abstracts\Providers;

use Boot\ServiceProviders\EventServiceProvider;



abstract class EventsProvider extends EventServiceProvider
{
    /**
     * Register any other events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
