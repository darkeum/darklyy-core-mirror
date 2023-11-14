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
use Users\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'permission:developers access', 'auth'])
    ->prefix('/administration/module/users')
    ->name('admin.users.')
    ->group(function () {

        Route::resource('/', ManagerController::class)
            ->parameters(['' => 'id']);

    });
