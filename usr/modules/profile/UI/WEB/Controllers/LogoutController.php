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
use Boot\System\Auth\AuthenticatesUsers;
use Boot\System\Validation\ValidatesRequests;

class LogoutController extends AbstractController
{
    use ValidatesRequests, AuthenticatesUsers;
}
