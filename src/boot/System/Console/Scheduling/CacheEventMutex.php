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

use Illuminate\Cache\DynamoDbStore;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Cache\LockProvider;

class CacheEventMutex implements EventMutex, CacheAware
{
    /**
     * Реализация хранилища кэша.
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
     * Создайте новую пересекающуюся стратегию.
     *
     * @param  \Illuminate\Contracts\Cache\Factory  $cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Попытка получить mutex события для указанного события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return bool
     */
    public function create(Event $event)
    {
        if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
            return $this->cache->store($this->store)->getStore()
                ->lock($event->mutexName(), $event->expiresAt * 60)
                ->acquire();
        }

        return $this->cache->store($this->store)->add(
            $event->mutexName(),
            true,
            $event->expiresAt * 60
        );
    }

    /**
     * Определите, существует ли mutex события для данного события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return bool
     */
    public function exists(Event $event)
    {
        if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
            return ! $this->cache->store($this->store)->getStore()
                ->lock($event->mutexName(), $event->expiresAt * 60)
                ->get(fn() => true);
        }

        return $this->cache->store($this->store)->has($event->mutexName());
    }

    /**
     * Очистить mutex события для указанного события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return void
     */
    public function forget(Event $event)
    {
        if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
            $this->cache->store($this->store)->getStore()
                ->lock($event->mutexName(), $event->expiresAt * 60)
                ->forceRelease();

            return;
        }

        $this->cache->store($this->store)->forget($event->mutexName());
    }

    /**
     * Определите, должно ли данное хранилище использовать блокировки для mutex событий кэша.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @return bool
     */
    protected function shouldUseLocks($store)
    {
        return $store instanceof LockProvider && ! $store instanceof DynamoDbStore;
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
