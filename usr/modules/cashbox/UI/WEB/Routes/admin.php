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
use Cashbox\UI\WEB\Controllers\ChecksController;
use Cashbox\UI\WEB\Controllers\CashboxController;
use Cashbox\UI\WEB\Controllers\CashierController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/cashbox')
    ->name('admin.')
    ->group(function () {
        Route::post('cashier/close', [CashierController::class, 'close_shift'])->name("cashbox.cashier.close");
        Route::post('cashier/find', [CashierController::class, 'find'])->name("cashbox.cashiers.find");
        Route::resource('/checks', ChecksController::class)->names("cashbox.checks");
        Route::resource('/cashier', CashierController::class)->names("cashbox.cashier");
        Route::resource('/', CashboxController::class)->names("cashbox")->parameters(['' => 'id']);
    });
