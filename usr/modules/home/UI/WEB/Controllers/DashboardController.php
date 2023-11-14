<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Certificates Manager Controller
*/

namespace Home\UI\WEB\Controllers;

use Boot\System\Livewire\Component;

class DashboardController extends Component
{
    // static public function dashboard()
    // {
    //     return view('home::manager.dashboard');
    // }

    public function render()
    {
        return view('home::manager.dashboard');
    }
}
