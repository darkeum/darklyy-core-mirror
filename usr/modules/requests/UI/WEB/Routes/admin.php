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
use Requests\UI\WEB\Controllers\RequestsController;
use Requests\UI\WEB\Controllers\RequestsUpdateController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/requests')
    ->name('admin.requests.')
    ->group(function () {

        //Route::prefix('select')
        //    ->name('select')
        //    ->group(function () {
        //        Route::get('getObj', [RequestsController::class, 'getObj'])->name('getObj'); // для работы select2 в форме
        //        Route::get('getBoiler', [RequestsController::class, 'getBoiler'])->name('getBoiler');
        //    });

        Route::get('select/getObj', [RequestsController::class, 'getObj'])->name('getObj'); // для работы select2 в форме
        Route::get('select/getBoiler', [RequestsController::class, 'getBoiler'])->name('getBoiler');
        Route::get('update/price/{id}', [RequestsUpdateController::class, 'price'])->name('update.price');
        Route::get('print/{id}', [RequestsController::class, 'print'])->name('print');

        Route::resource('/', RequestsController::class)->parameters(['' => 'id']);
    });
