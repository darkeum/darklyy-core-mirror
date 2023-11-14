<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Verifications\UI\WEB\Controllers;


use Boot\Support\Facades\DB;
use Boot\Controller\AbstractController;
use Boot\System\Http\Request;

class VerificationsController extends AbstractController
{
    static function index()
    {
        return view('verifications::manager.index');
    }
}