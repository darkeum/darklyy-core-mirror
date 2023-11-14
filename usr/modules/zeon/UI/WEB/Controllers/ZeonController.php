<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Zeon\UI\WEB\Controllers;

use Boot\Abstracts\Controllers\WebController;

class ZeonController extends WebController
{
    static public function index()
    {
        return view('zeon::manager.index');
    }

    static public function tree()
    {
        return view('zeon::manager.tree');
    }

    static public function login()
    {
        return view('profile::new.auth-basic.login');
    }
}
