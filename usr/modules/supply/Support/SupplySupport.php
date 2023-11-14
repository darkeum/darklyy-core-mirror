<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Supply\Support;

use Supply\Models\Supply;
use Boot\Support\Facades\Cache;

class SupplySupport
{
    public static function getSupplyCount($type)
    {        
        $key = 'supply.type.'.$type.'.count';
        return Cache::rememberForever($key, function () use($type) {
            return Supply::where("status", 1)->where("type", $type)->count();
        });
    }
}
