<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Boot\Support\Facades\Route;
use Order\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access'])
    ->prefix('/administration/module/order')
    ->name('admin.')
    ->group(function () {
        Route::resource('/', ManagerController::class)->names('order')->parameters(['' => 'id'])->only([
            'show', 'grid', 'index'
        ]);
    });
