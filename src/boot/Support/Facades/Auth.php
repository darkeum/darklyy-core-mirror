<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Auth Helper class
*/

namespace Boot\Support\Facades;

use Illuminate\Support\Facades\Auth as AuthFacade;

class Auth extends Facade
{
    /**
     * Проверка авторизации пользователя
     *   
     * @return bool    Возвращает true если пользователь авторизован, иначе false
     */
    public static function check()
    {
        return AuthFacade::check();
    }

    /**
     * Получение идентификатора пользователя
     *     
     * @return int|null    Возвращает id (uid) авторизованного пользователя если пользователь не авторизован возвращает null
     */
    public static function id()
    {
        return AuthFacade::id();
    }

    /**
     * Проверка принадлежности пользователя к администрации
     *    
     * @return bool    Возвращает true если пользователь авторизован и является администратором, иначе false
     */
    public static function isAdmin()
    {
        // $auth = new AuthServices;
        return AuthFacade::check();
    }

    /**
     * Проверка пользователя на гостя (обратный синоним метода Auth::check())
     *
     * @return bool    Возвращает true если пользователь не авторизован, иначе false
     */
    public static function isGuest()
    {
        if (AuthFacade::check()) {
            return false;
        }
        return true;
    }

    /**
     * Проверка авторизации пользователя (синоним метода Auth::check())
     *   
     * @return bool    Возвращает true если пользователь авторизован, иначе false
     */
    public static function isUser()
    {
        // $auth = new AuthServices;
        return AuthFacade::check();
    }

  

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }
}
