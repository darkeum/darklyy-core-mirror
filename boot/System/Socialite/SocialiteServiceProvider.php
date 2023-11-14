<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite;

use Illuminate\Contracts\Support\DeferrableProvider;
use Boot\Support\ServiceProvider;
use Boot\System\Socialite\Contracts\Factory;

class SocialiteServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Регистрация службы
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new SocialiteManager($app);
        });
    }

    /**
     * Получить службу, предоставляемые провайдером.
     *
     * @return array
     */
    public function provides()
    {
        return [Factory::class];
    }
}
