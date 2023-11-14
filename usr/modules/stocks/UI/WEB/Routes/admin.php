<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

use Boot\Support\Facades\Route;
use Stocks\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/stocks')
    ->name('admin.')
    ->group(function () {
        Route::resource('/', ManagerController::class)->names('stocks')->parameters(['' => 'id']);
    });
