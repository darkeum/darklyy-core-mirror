<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Order\Observers;

use Order\Models\Order;
use Boot\Support\Facades\Cache;

class OrderObserver
{

    /**
     * Срабатывает после изменения из базы данных
     *
     * @param  mixed $page
     * @return void
     */
    public function saved(Order $order)
    {
        $this->orderCache($order);
    }

    /**
     * taskCache
     *
     * @param  mixed $task
     * @return void
     */
    protected function orderCache(Order $order)
    {
        Cache::put('order.count', Order::where('read_status', 0)->count());
    }
}
