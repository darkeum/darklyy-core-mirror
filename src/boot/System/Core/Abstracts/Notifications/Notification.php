<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace  Boot\System\Core\Abstracts\Notifications;

use Boot\System\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Config;

abstract class Notification extends LaravelNotification
{
    public function via($notifiable): array
    {
        return Config::get('notification.channels');
    }
}
