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
use Roles\UI\WEB\Controllers\RolesController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'permission:developers access', 'auth'])
    ->prefix('/administration/module/roles')
    ->name('admin.roles.')
    ->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('index');
    });
