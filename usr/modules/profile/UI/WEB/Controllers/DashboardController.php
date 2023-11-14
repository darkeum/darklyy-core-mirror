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

use Boot\Abstracts\Controllers\WebController;
// use \Boot\Models\Users;
use Boot\Support\Facades\Auth;
use Sales\Models\Sales;
use Task\Models\Task;

class DashboardController extends WebController
{
    static public function index()
    {
        $sales = Sales::where('uid', Auth::id())->latest()->take(10)->get();
        $tasks = Task::where('toid', Auth::id())->latest()->take(10)->get();
        // $sales = Sales::where('uid', 107)->latest()->take(10)->get(); // Братухин для теста

        return view('profile::manager.index', [
            'sales' => $sales,
            'tasks' => $tasks
        ]);
    }

    static public function edit()
    {
        return view('profile::manager.edit');
    }

    static public function security()
    {
        return view('profile::manager.security');
    }

    static public function notifications()
    {
        return view('profile::manager.notifications');
    }

    static public function settings()
    {
        return view('profile::manager.settings');
    }
}