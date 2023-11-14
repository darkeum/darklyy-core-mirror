<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Cache Helper class
*/

namespace Boot\Support;

use \Boot\System\Cache\Cache as CacheSystem;
use Illuminate\Support\Facades\Cache as LaravelCache;

class Cache
{

    /**
     * Получение кэша по ключу
     *
     * @param  string               $key Ключ кэша
     * @return string|array|bool    Возвращает значение ключа кэша, если данного ключа нет возвращает false
     */
    public static function get($key)
    {
        return LaravelCache::get($key);
    }

    /**
     * Проверка наличия кэша по ключу
     *
     * @param  string  $key Ключ кэша
     * @return bool    Возвращает true если кэш найден, иначе false
     */
    public static function has($key)
    {
        return LaravelCache::has($key);
    }

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

    /**
     * Очистить все ключи кеша
     *     
     * @return bool  Возвращает true если удалено успешно, иначе false записанного кэша
     */
    public static function clear()
    {
        return LaravelCache::flush();
    }
}
