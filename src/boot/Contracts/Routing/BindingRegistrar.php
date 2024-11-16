<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Contracts\Routing;

interface BindingRegistrar
{
    /**
     * Добавление новой привязки параметров маршрута.
     *
     * @param  string  $key
     * @param  string|callable  $binder
     * @return void
     */
    public function bind($key, $binder);

    /**
     * Получение обратного вызова привязки для заданной привязки.
     *
     * @param  string  $key
     * @return \Closure
     */
    public function getBindingCallback($key);
}
