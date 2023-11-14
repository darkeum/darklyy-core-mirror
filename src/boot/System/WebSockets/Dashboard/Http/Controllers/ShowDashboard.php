<?php

namespace Boot\System\WebSockets\Dashboard\Http\Controllers;

use Boot\System\WebSockets\Contracts\AppManager;
use Boot\System\WebSockets\DashboardLogger;
use function Clue\React\Block\await;
use Illuminate\Http\Request;
use React\EventLoop\LoopInterface;

class ShowDashboard
{
    /**
     * Show the dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Boot\System\WebSockets\Contracts\AppManager  $apps
     * @return void
     */
    public function __invoke(Request $request, AppManager $apps)
    {
        return view('websockets::dashboard', [
            'apps' => await($apps->all(), app(LoopInterface::class), 2.0),
            'port' => config('websockets.dashboard.port', 6001),
            'channels' => DashboardLogger::$channels,
            'logPrefix' => DashboardLogger::LOG_CHANNEL_PREFIX,
            'refreshInterval' => config('websockets.statistics.interval_in_seconds'),
        ]);
    }
}
