<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Control\Providers;

use Control\Models\Control;
use Control\Observers\ControlObserver;
use Boot\Support\ServiceProvider;

class ControlModelServiceProvider extends ServiceProvider 
{
    public function boot() {
        Control::observe(ControlObserver::class);
    }
}