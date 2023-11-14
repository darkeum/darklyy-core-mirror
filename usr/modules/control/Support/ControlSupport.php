<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Control\Support;

use Boot\Support\Facades\Cache;
use Control\Models\Control;

class ControlSupport
{
    public static function getControlCount()
    {      
        $key = 'control.count';
        return Cache::rememberForever($key, function () {
            return Control::where("status", 1)->count();
        });
    }

    public static function getControlPayCount()
    {       
        $key = 'control.pay.count';
        return Cache::rememberForever($key, function ()  {
            return Control::where("status", 3)->where("pay", 0)->count();
        });
    }
}
