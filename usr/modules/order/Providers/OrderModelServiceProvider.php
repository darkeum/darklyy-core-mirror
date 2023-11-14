<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Order\Providers;

use Order\Models\Order;
use Order\Observers\OrderObserver;
use Boot\Support\ServiceProvider;

class OrderModelServiceProvider extends ServiceProvider 
{
    public function boot() {
        Order::observe(OrderObserver::class);
    }
}