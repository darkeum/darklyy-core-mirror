<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Order\Support;

use Order\Models\Order;
use Boot\Support\Facades\Cache;

class OrderSupport
{
    public static function getOrderCount()
    {        
        $key = 'order.count';
        return Cache::rememberForever($key, function () {
            return Order::where('read_status', 0)->count();
        });
    }
}
