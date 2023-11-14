<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

use Boot\Support\Facades\Route;
use Calls\UI\WEB\Controllers\CallsController;
use Calls\UI\WEB\Controllers\Contacts;
use Notifications\UI\WEB\Controllers\ControllerRedirect;


Route::any('test-load', [CallsController::class, 'load']);
//Route::post('test-load', [Controller::class, 'load']);

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/calls')
    ->name('admin.')
    ->group(function () {
        Route::any('parse', [CallsController::class, 'parse'])->name('calls.parse');
        Route::get('redirect/{id}', [ControllerRedirect::class, 'redirect'])->name('calls.redirect');
        Route::resource('archive', CallsController::class)->names('calls.archive')->parameters(['' => 'id'])->only(['index', 'grid']);
        Route::resource('/', CallsController::class)->names('calls')->parameters(['' => 'id'])->only(['index']);
    });

Route::middleware(['theme:admin-material', 'permission:calls manager', 'auth'])
    ->prefix('administration/module/calls/contacts')
    ->name('admin.')
    ->controller(Contacts::class)
    ->group( function () {
        Route::resource('/', Contacts::class)->parameters(['' => 'id'])->names('contacts');
    });
