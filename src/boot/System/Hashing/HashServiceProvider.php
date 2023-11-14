<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Hashing;

use Boot\Contracts\Support\DeferrableProvider;
use Boot\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Регистрация поставщика услуг
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('hash', function ($app) {
            return new HashManager($app);
        });

        $this->app->singleton('hash.driver', function ($app) {
            return $app['hash']->driver();
        });
    }

    /**
     * Получить услуги, предоставляемые провайдером.
     *
     * @return array
     */
    public function provides()
    {
        return ['hash', 'hash.driver'];
    }
}
