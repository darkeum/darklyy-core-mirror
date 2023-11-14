<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev kirill
*/

namespace roles\UI\WEB\Controllers;

use Boot\Controller\AbstractController;

class RolesController extends AbstractController
{
    public static function index()
    {
        return view('roles::manager.index');
    }

}
