<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Cache Helper class
*/

namespace Boot\Support\Facades;

use Illuminate\Support\Facades\Cache as LaravelCache;

/**
 * @method static \Illuminate\Cache\TaggedCache tags(array|mixed $names)
 * @method static \Illuminate\Contracts\Cache\Lock lock(string $name, int $seconds = 0, mixed $owner = null)
 * @method static \Illuminate\Contracts\Cache\Lock restoreLock(string $name, string $owner)
 * @method static \Illuminate\Contracts\Cache\Repository  store(string|null $name = null)
 * @method static \Illuminate\Contracts\Cache\Store getStore()
 * @method static bool add(string $key, $value, \DateTimeInterface|\DateInterval|int $ttl = null)
 * @method static bool flush()
 * @method static bool forever(string $key, $value)
 * @method static bool forget(string $key)
 * @method static bool has(string $key)
 * @method static bool missing(string $key)
 * @method static bool put(array|string $key, $value, \DateTimeInterface|\DateInterval|int $ttl = null)
 * @method static int|bool decrement(string $key, $value = 1)
 * @method static int|bool increment(string $key, $value = 1)
 * @method static mixed get(array|string $key, mixed $default = null)
 * @method static mixed pull(string $key, mixed $default = null)
 * @method static mixed remember(string $key, \DateTimeInterface|\DateInterval|int $ttl, \Closure $callback)
 * @method static mixed rememberForever(string $key, \Closure $callback)
 * @method static mixed sear(string $key, \Closure $callback)
 *
 * @see \Illuminate\Cache\CacheManager
 * @see \Illuminate\Cache\Repository
 */

class Cache extends Facade
{

   

    /**
     * Создание кэша
     *
     * @param  string               $key Ключ кэша
     * @param  string|array         $value Значение кэша
     * @param  int|null             $expires Время действия кэша, если null (по умолчанию) то без ограничения по времени
     * @return string|array|bool    Возвращает значение записанного кэша
     */
    public static function set($key, $value, $expires = null)
    {
        return LaravelCache::put($key, $value, $expires);
    }

    /**
     * Удаление кэша
     *
     * @param  string       $key Ключ кэша
     * @return bool         Возвращает true если удалено успешно, иначе false записанного кэша
     */
    public static function del($key)
    {
        return LaravelCache::forget($key);
    }

    /**
     * Удаление кэша синоним функции Cache::del
     *
     * @param  string   $key Ключ кэша
     * @return bool     Возвращает true если удалено успешно, иначе false записанного кэша
     */
    public static function delete($key)
    {
        return static::del($key);
    } 



    protected static function getFacadeAccessor()
    {
        return 'cache';
    }
}
