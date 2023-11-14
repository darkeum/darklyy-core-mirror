<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Profile\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\System\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends AbstractController
{
    /*
    |------------------------------------------------- ------------------------
    | Контроллер сброса пароля
    |------------------------------------------------- ------------------------
    |
    | Этот контроллер отвечает за обработку сообщений электронной почты для сброса пароля и
    | включает трейт, который помогает отправлять эти уведомления 
    | для ваших пользователей.
    |
    */

    use SendsPasswordResetEmails;
}
