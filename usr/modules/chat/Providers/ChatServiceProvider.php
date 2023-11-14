<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Chat\Providers;

use Chat;
use Boot\Support\ServiceProvider;
use Boot\System\Console\Scheduling\Schedule;

class ChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Chat::class, function ($app) {
            return new Chat();
        });
    }
}
