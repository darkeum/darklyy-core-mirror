<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Cache helper function 
*/

 
use \Boot\Support\Facades\Route;

if (!function_exists('router')) {
    /**
     * Проверка наличия кэша по ключу
     *
     * @param  string  $key Ключ кэша
     * @return bool    Возвращает true если кэш найден, иначе false
     */
    function router($name, $params = null)
    {
        // var_dump($name);
       return route($name, $params);
       

       
    }
}
