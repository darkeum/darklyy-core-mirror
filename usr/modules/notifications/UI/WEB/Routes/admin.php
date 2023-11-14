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
use Notifications\UI\WEB\Controllers\Controller;
use Notifications\UI\WEB\Controllers\ControllerRedirect;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/notifications')
    ->name('admin.')
    ->group(function () {
        Route::get('redirect/{id}', [ControllerRedirect::class, 'redirect'])->name('notifications.redirect');
        Route::resource('archive', Controller::class)->names('notifications.archive')->parameters(['' => 'id'])->only(['index', 'grid']);
        Route::resource('/', Controller::class)->names('notifications')->parameters(['' => 'id'])->only(['index']);
    });

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/notif')
    ->name('admin.')
    ->group(function () {
        Route::get('redirect/{id}', function ($id) {
            return redirect()->route('admin.notifications.redirect', ['id' => $id]);
        })->name('notif.redirect');     
        Route::get('/', function () {
            return redirect()->route('admin.notifications.index');
        })->name('notif.index');
    });
