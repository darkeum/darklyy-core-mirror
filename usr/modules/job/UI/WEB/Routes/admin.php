<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Boot\Support\Facades\Route;
use Job\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/job')
    ->name('admin.')
    ->group(function () {
        Route::get('subscribes', [ManagerController::class, 'subscribes'])->name('job.subscribes');
        Route::resource('/',  ManagerController::class)->names('job')->parameters(['' => 'id']);       
    });
