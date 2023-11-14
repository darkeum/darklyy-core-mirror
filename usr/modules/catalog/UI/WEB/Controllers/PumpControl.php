<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Catalog\UI\WEB\Controllers;

use Boot\Controller\AbstractController;

class PumpControl extends AbstractController
{

    /**
     * Список сокращенных URL
     *
     * @return void
     */
    static function index()
    {
        return view('catalog::manager.pump.control');
    }

    static function equate()
    {
        return view('catalog::manager.pump.equate');
    }

}
