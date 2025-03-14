<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Scheduling;

interface CacheAware
{
    /**
     * Укажите хранилище кэша, которое следует использовать.
     *
     * @param  string  $store
     * @return $this
     */
    public function useStore($store);
}
