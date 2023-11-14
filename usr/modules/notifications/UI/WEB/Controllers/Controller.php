<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\UI\WEB\Controllers;

use Boot\Abstracts\Controllers\WebController;
use Notifications\Models\Notifications;

class Controller extends WebController
{
    static public function index()
    {
        return view('notifications::manager.index');
    }
}
