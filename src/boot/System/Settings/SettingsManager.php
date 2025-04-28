<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings;

use Boot\Models\Users;
use Boot\Support\Facades\Auth;

class SettingsManager
{
    /**
     * Получить настройки пользователя по ключу
     *
     * @param  string $key Ключ настроек
     * @param  mixed $default Значение по умолчанию
     * @return void
     */
    public function get(string $key, mixed $default = null)
    {
        return Auth::check() ? Auth::user()->settings($key, $default) : $default;
    }

    /**
     * Получить настройки указанного пользователя по ключу
     *
     * @param  int $uid Идентификатор пользователя
     * @param  string $key Ключ настроек
     * @param  mixed $default Значение по умолчанию
     * @return void
     */
    public function getByUser(int $uid, string $key, mixed $default = null)
    {
        $user = Users::find($uid);
        return !empty($user) ? $user->settings($key, $default) : $default;
    }

    /**
     * Установить настройки пользователя по ключу
     *
     * @param  string $key Ключ настроек
     * @param  mixed $value Значение
     * @param  string $value Тип переменной
     * @return void
     */
    public function set(string $key, mixed $value, string $type = 'unknown')
    {
        return Auth::user()->setSetting($key, $value, $type);
    }
}
