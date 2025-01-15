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

use Boot\System\Broadcasting\BroadcastManager;
use Illuminate\Contracts\Broadcasting\Broadcaster as BroadcasterContract;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactory;
use Boot\Contracts\Support\DeferrableProvider;
use Boot\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Регистрация сервис провайдера
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BroadcastManager::class, function ($app) {
            return new BroadcastManager($app);
        });

        $this->app->singleton(BroadcasterContract::class, function ($app) {
            return $app->make(BroadcastManager::class)->connection();
        });

        $this->app->alias(
            BroadcastManager::class,
            BroadcastingFactory::class
        );
    }

    /**
     * Получение услуг, предоставляемые поставщиком.
     *
     * @return array
     */
    public function provides()
    {
        return [
            BroadcastManager::class,
            BroadcastingFactory::class,
            BroadcasterContract::class,
        ];
    }
}
