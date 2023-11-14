<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Storage\Observers;

use Boot\Support\Facades\Cache;
use Storage\Models\StorageAssembly;

class StorageAssemblyObserver
{

    /**
     * Срабатывает после изменения из базы данных
     *
     * @param  mixed $page
     * @return void
     */
    public function saved(StorageAssembly $items)
    {
        $this->cache($items);
    }

    /**
     * Кэширование
     *
     * @param  mixed $task
     * @return void
     */
    protected function cache(StorageAssembly $items)
    {
        Cache::put('storage.assembly.count', StorageAssembly::where("del", 0)->where("status", 1)->count());
    }
}
