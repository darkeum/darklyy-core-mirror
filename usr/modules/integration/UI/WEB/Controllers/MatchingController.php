<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Integration\UI\WEB\Controllers;

use Boot\Controller\AbstractController;

class MatchingController extends AbstractController
{
    static function category()
    {
        return view('integration::manager.matching.category');
    }

    static function attribute()
    {
        return view('integration::manager.matching.attribute');
    }

    static function values()
    {
        return view('integration::manager.matching.values');
    }
}
