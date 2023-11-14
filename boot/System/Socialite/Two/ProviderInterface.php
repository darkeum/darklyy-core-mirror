<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Two;

interface ProviderInterface
{
    /**
     * Перенаправить пользователя на страницу аутентификации для провайдера.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();

    /**
     * Получите экземпляр User для аутентифицированного пользователя.
     *
     * @return \Boot\System\Socialite\Two\User
     */
    public function user();
}
