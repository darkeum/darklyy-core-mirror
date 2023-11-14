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
use Changelog\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/changelog')
    ->name('admin.')
    ->group(function () {

        Route::get('/categories', [ManagerController::class,'categories'])->name('changelog.categories');
        Route::resource('/', ManagerController::class)->names('changelog')->parameters(['' => 'id']);
        
    });

