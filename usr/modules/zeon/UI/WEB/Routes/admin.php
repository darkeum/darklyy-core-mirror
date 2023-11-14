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
use Zeon\UI\WEB\Controllers\ZeonController;
use Zeon\UI\WEB\Controllers\InvoiceController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/zeon')
    ->name('admin.zeon.')
    ->group(function () {

        Route::prefix('invoice')
            ->name('invoice.')
            ->group(function () {
                //Route::get('invoice',  [InvoiceController::class, 'index'])->name('index');
                //Route::view('invoice', 'controls::manager.index')->name('invoice');
                Route::resource('/', InvoiceController::class)->parameter('', 'id');
            });

        Route::get('/', [ZeonController::class, 'index'])->name('index');
        Route::get('tree', [ZeonController::class, 'tree'])->name('tree');

        Route::get('login', [ZeonController::class, 'login']);

    });
