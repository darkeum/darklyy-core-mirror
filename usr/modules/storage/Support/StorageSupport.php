<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Storage\Support;

use Storage\Models\Storage;
use Boot\Support\Facades\Cache;
use Storage\Models\StockTransfer;
use Storage\Models\StorageAssembly;

class StorageSupport
{
    public static function getStorageCount()
    {
        $key = 'storage.count';
        return Cache::rememberForever($key, function () {
            return Storage::where("status", 1)->count();
        });
    }
    
    public static function getStorageAssemblyCount()
    {
        $key = 'storage.assembly.count';
        return Cache::rememberForever($key, function () {
            return StorageAssembly::where("del", 0)->where("status", 1)->count();
        });
    }

    public static function getStorageTransferCount()
    {
        $key = 'storage.transfer.count';
        return Cache::rememberForever($key, function () {
            return StockTransfer::where("active", 0)->where("del", 0)->count();
        });
    }
}
