<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Control\Observers;

use Control\Models\Control;
use Boot\Support\Facades\Cache;

class ControlObserver
{

    /**
     * Срабатывает после изменения из базы данных
     *
     * @param  mixed $page
     * @return void
     */
    public function saved(Control $items)
    {
        $this->cache($items);
    }

    /**
     * Кэширование
     *
     * @param  mixed $task
     * @return void
     */
    protected function cache(Control $items)
    {
        Cache::put('control.count', Control::where("status", 1)->count());
        Cache::put('control.pay.count', Control::where("status", 3)->where("pay", 0)->count());
    }
}
