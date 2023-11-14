<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\Observers;

use Notifications\Models\Notifications;
use Boot\Support\Facades\Cache;

class NotificationsObserver
{

    /**
     * Срабатывает после изменения из базы данных
     *
     * @param  mixed $page
     * @return void
     */
    public function saved(Notifications $items)
    {
        $this->cache($items);
    }

    /**
     * Кэширование
     *
     * @param  mixed $task
     * @return void
     */
    protected function cache(Notifications $items)
    {
        Cache::put('notifications.user.'.$items->uid.'.count', Notifications::where('status', 1)->where('uid', $items->uid)->count());
        Cache::put('notifications.user.'.$items->uid, Notifications::where('status', 1)->where('uid', $items->uid)->limit(20)->get());
    }
}
