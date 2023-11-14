<?php

namespace Boot\Traits;

use \Boot\Support\Cache;

trait CacheAll{
    static function get_cache_all(){
        $cache_name = (new \ReflectionClass(new static))->getShortName()."_all_data";
        if(Cache::has($cache_name)) {
            return Cache::get($cache_name);
        } else {
            $data = static::all();
            Cache::set($cache_name, $data, 30);
            return $data;
        }
    }
}