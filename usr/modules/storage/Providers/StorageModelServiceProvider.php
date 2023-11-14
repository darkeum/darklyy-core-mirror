<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Storage\Providers;

use Storage\Models\Storage;
use Boot\Support\ServiceProvider;
use Storage\Models\StockTransfer;
use Storage\Models\StorageAssembly;
use Storage\Observers\StockTransferObserver;
use Storage\Observers\StorageAssemblyObserver;

class StorageModelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        StockTransfer::observe(StockTransferObserver::class);
        Storage::observe(StorageObserver::class);
        StorageAssembly::observe(StorageAssemblyObserver::class);
    }
}
