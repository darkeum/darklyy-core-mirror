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
use Profile\UI\WEB\Controllers\RoleController;
use Profile\UI\WEB\Controllers\TokenController;
use Profile\UI\WEB\Controllers\DashboardController;
use Profile\UI\WEB\Controllers\PermissionController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/profile')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('profile.index');
        Route::get('edit', [DashboardController::class, 'edit'])->name('profile.edit');

        Route::get('token/{id}', [TokenController::class, 'token'])->name('profile.token');
        Route::get('security', [DashboardController::class, 'security'])->name('profile.security');
        Route::get('notifications', [DashboardController::class, 'notifications'])->name('profile.notifications');
        Route::get('settings', [DashboardController::class, 'settings'])->name('profile.settings');

        Route::resource('roles', RoleController::class)
            ->names('profile.role')
            ->parameters(['' => 'id']);
        Route::resource('permissions', PermissionController::class)
            ->names('profile.permission')
            ->parameters(['' => 'id']);
    });