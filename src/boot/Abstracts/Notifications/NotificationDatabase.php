<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace  Boot\Abstracts\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Boot\System\Notifications\CreateDatabaseNotifications;

abstract class NotificationDatabase extends Notification implements ShouldQueue
{
    use Queueable;
    use CreateDatabaseNotifications;
}
