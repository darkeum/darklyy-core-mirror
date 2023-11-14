<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings;

use Boot\Support\Facades\Auth;

class SettingsManager
{    
    /**
     * Получить настройки пользователя по ключу
     *
     * @param  mixed $key Ключ настроек
     * @param  mixed $default Значение по умолчанию
     * @return void
     */
    public function get($key, $default = null)
    {
        
        return Auth::check() ? Auth::user()->settings($key, $default) : $default;
    }
    
    /**
     * Установить настройки пользователя по ключу
     *
     * @param  mixed $key Ключ настроек
     * @param  mixed $value Значение
     * @return void
     */
    public function set($key, $value)
    {
        return Auth::user()->setSetting($key, $value);
    }
}
