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

use DateTimeInterface;
use Illuminate\Contracts\Cache\Factory as Cache;

class CacheSchedulingMutex implements SchedulingMutex, CacheAware
{
    /**
     * Реализация фабрики кэша.
     *
     * @var \Illuminate\Contracts\Cache\Factory
     */
    public $cache;

    /**
     * Кэш-хранилище, которое следует использовать.
     *
     * @var string|null
     */
    public $store;

    /**
     * Создайте новую стратегию планирования.
     *
     * @param  \Illuminate\Contracts\Cache\Factory  $cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Попытка получить mutex планирования для указанного события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @param  \DateTimeInterface  $time
     * @return bool
     */
    public function create(Event $event, DateTimeInterface $time)
    {
        return $this->cache->store($this->store)->add(
            $event->mutexName() . $time->format('Hi'),
            true,
            3600
        );
    }

    /**
     * Определите, существует ли mutex планирования для данного события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @param  \DateTimeInterface  $time
     * @return bool
     */
    public function exists(Event $event, DateTimeInterface $time)
    {
        return $this->cache->store($this->store)->has(
            $event->mutexName() . $time->format('Hi')
        );
    }

    /**
     * Укажите хранилище кэша, которое следует использовать.
     *
     * @param  string  $store
     * @return $this
     */
    public function useStore($store)
    {
        $this->store = $store;

        return $this;
    }
}
