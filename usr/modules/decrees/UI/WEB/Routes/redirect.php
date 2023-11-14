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

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/command')
    ->name('admin.redirect.')
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.decrees.index');
        })->name('decrees');
    });
