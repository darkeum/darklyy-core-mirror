<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Contracts;

interface Provider
{
    /**
     * Перенаправление пользователя на страницу аутентификации для провайдера.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Boot\System\Http\RedirectResponse
     */
    public function redirect();

    /**
     * Получение экземпляра User для аутентифицированного пользователя.
     *
     * @return \Boot\System\Socialite\Contracts\User
     */
    public function user();
}
