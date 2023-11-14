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
use Warranties\UI\WEB\Controllers\WarrantiesController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/warranties')
    ->name('admin.warranties.')
    ->group(function () {
        Route::get('/data', [WarrantiesController::class, 'getData'])->name('data');
        Route::get('/realization', [WarrantiesController::class, 'getRealization'])->name('realization');        
        Route::get('/', [WarrantiesController::class, 'create'])->name('create');
    });

