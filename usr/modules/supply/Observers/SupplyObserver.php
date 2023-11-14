<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Supply\Observers;

use Supply\Models\Supply;
use Boot\Support\Facades\Cache;

class SupplyObserver
{

    /**
     * Срабатывает после изменения из базы данных
     *
     * @param  mixed $page
     * @return void
     */
    public function saved(Supply $items)
    {
        $this->cache($items);
    }

    /**
     * taskCache
     *
     * @param  mixed $task
     * @return void
     */
    protected function cache(Supply $items)
    {
        Cache::put('supply.type.1.count', Supply::where("status", 1)->where("type", 1)->count());
        Cache::put('supply.type.2.count', Supply::where("status", 1)->where("type", 2)->count());
    }
}
