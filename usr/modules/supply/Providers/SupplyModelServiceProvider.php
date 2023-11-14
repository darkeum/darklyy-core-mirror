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

use Supply\Models\Supply;
use Supply\Observers\SupplyObserver;
use Boot\Support\ServiceProvider;

class SupplyModelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Supply::observe(SupplyObserver::class);
    }
}
