<?php

namespace Boot\System\Exceptions\Ignition\ContextProviders;

use Boot\System\Http\Request;
use Livewire\LivewireManager;
use \Spatie\FlareClient\Context\ContextProvider;
use \Spatie\FlareClient\Context\ContextProviderDetector;
use Spatie\LaravelIgnition\ContextProviders\LaravelConsoleContextProvider;
use Spatie\LaravelIgnition\ContextProviders\LaravelLivewireRequestContextProvider;

class LaravelContextProviderDetector implements ContextProviderDetector
{
    public function detectCurrentContext(): ContextProvider
    {
        if (app()->runningInConsole()) {
            return new LaravelConsoleContextProvider($_SERVER['argv'] ?? []);
        }

        $request = app(Request::class);

        if ($this->isRunningLiveWire($request)) {
            return new LaravelLivewireRequestContextProvider($request, app(LivewireManager::class));
        }

        return new LaravelRequestContextProvider($request);
    }

    protected function isRunningLiveWire(Request $request): bool
    {
        return $request->hasHeader('x-livewire') && $request->hasHeader('referer');
    }
}
