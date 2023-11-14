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
use Support\UI\WEB\Controllers\SupportController;
use Support\UI\WEB\Controllers\SupportMessageController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/support')
    ->name('admin.support.')
    ->group(function () {

        //Route::get('info/{id}', [SupportController::class, 'info'])->name('info');
        Route::get('all', [SupportController::class, 'all'])->name('all')
            ->middleware(['permission:support manager']);

        Route::resource('messages', SupportMessageController::class)->names('messages')->parameters(['' => 'id']);
        Route::resource('/', SupportController::class)->parameters(['' => 'id']);

    });
