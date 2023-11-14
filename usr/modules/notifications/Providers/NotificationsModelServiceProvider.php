<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\Providers;

use Notifications\Models\Notifications;
use Notifications\Observers\NotificationsObserver;
use Boot\Support\ServiceProvider;

class NotificationsModelServiceProvider extends ServiceProvider 
{
    public function boot() {
        Notifications::observe(NotificationsObserver::class);
    }
}