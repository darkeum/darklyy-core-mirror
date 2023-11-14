<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace App\Components\Layouts;

use Illuminate\View\Component;

class Base extends Component
{
    public function render()
    {
        return view('layouts.base');
    }
}