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
use Bank\UI\WEB\Controllers\BankController;
use Bank\UI\WEB\Controllers\BankPayController;
use Bank\UI\WEB\Controllers\BankStatementsController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/bank')
    ->name('admin.bank.')
    ->group(function () {

        // Операции
        Route::get('recount/{id}', [BankController::class, 'recount'])->name('recount');
        Route::post('parse', [BankController::class, 'parse'])->name('parse');

        Route::prefix('edit')
            ->name('edit.')
            ->group(function () {
                Route::get('details/{id}',  [BankController::class, 'edit_details'])->name('details');
                Route::get('reestr/{id}',  [BankController::class, 'edit_reestr'])->name('reestr');
            });

        Route::get('edit_details/{id}',  [BankController::class, 'edit_details'])->name('edit_details');
        Route::get('edit_reestr/{id}',  [BankController::class, 'edit_reestr'])->name('edit_reestr');

        Route::resource('pay', BankPayController::class)->names('pay')->parameters(['' => 'id']);
        Route::resource('/', BankController::class)->parameters(['' => 'id'])->except(['show']);

        // Выписки
        Route::prefix('statements')
            ->name('statements.')
            ->group(function () {
                Route::get('download/{id}', [BankStatementsController::class, 'download'])->name('download');
                Route::resource('/', BankStatementsController::class)->parameters(['' => 'id']);
            });

    });

