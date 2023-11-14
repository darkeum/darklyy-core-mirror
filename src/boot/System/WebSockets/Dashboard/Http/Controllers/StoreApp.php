<?php

namespace Boot\System\WebSockets\Dashboard\Http\Controllers;

use Boot\System\WebSockets\Contracts\AppManager;
use Boot\System\WebSockets\Dashboard\Http\Requests\StoreAppRequest;
use function Clue\React\Block\await;
use Illuminate\Support\Str;
use React\EventLoop\LoopInterface;

class StoreApp
{
    /**
     * Show the configured apps.
     *
     * @param  StoreAppRequest  $request
     * @param  \Boot\System\WebSockets\Contracts\AppManager  $apps
     * @return void
     */
    public function __invoke(StoreAppRequest $request, AppManager $apps)
    {
        $appData = [
            'id' => (string) Str::uuid(),
            'key' => (string) Str::uuid(),
            'secret' => (string) Str::uuid(),
            'name' => $request->get('name'),
            'enable_client_messages' => $request->has('enable_client_messages'),
            'enable_statistics' => $request->has('enable_statistics'),
            'allowed_origins' => $request->get('allowed_origins'),
        ];

        await($apps->createApp($appData), app(LoopInterface::class));

        return redirect()->route('laravel-websockets.apps');
    }
}
