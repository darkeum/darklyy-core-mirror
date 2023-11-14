<?php

namespace Boot\ServiceProviders;

use GuzzleHttp\Client as HttpClient;
use Boot\Support\ServiceProvider;
use NotificationChannels\Telegram\Telegram;
use Boot\Support\Facades\Config;

/**
 * Class TelegramServiceProvider.
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(Telegram::class, static function () {
            return new Telegram(
                Config::get('services.telegram-bot-api')['token'],
                app(HttpClient::class),
                null
            );
        });

        // Notification::resolved(static function (ChannelManager $service) {
        //     $service->extend('telegram', static function ($app) {
        //         return $app->make(TelegramChannel::class);
        //     });
        // });
    }
}