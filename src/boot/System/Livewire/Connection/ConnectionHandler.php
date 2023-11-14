<?php

namespace Boot\System\Livewire\Connection;

use Boot\System\Livewire\LifecycleManager;

abstract class ConnectionHandler
{
    public function handle($payload)
    {
        return LifecycleManager::fromSubsequentRequest($payload)
            ->boot()
            ->hydrate()
            ->renderToView()
            ->dehydrate()
            ->toSubsequentResponse();
    }
}
