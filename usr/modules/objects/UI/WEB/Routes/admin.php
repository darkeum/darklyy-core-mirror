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
use Objects\UI\WEB\Controllers\ObjectsController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/objects')
    ->name('admin.objects.')
    ->group(function () {

        // Карта
        Route::get('map', [ObjectsController::class, 'map'])->name('map');
        Route::get('map/coords', [ObjectsController::class, 'coords'])->name('map.coords');

        Route::get('users/{id}', [ObjectsController::class, 'users'])->name('users');
        Route::post('users', [ObjectsController::class, 'users_add'])->name('users.add');

        // Дерево
        Route::get('tree/{id}', [ObjectsController::class, 'tree'])->name('tree');

        // Остальные
        Route::resource('/', ObjectsController::class)->parameters(['' => 'id']);

    });