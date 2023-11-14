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
    ->prefix('/administration/object')
    ->name('admin.redirect.')
    ->group(function () {
        Route::get('/view/{id}', function ($id) {
            return redirect()->route('admin.objects.show', ['id' => $id]);
        })->name('object.show');

        Route::get('/edit/{id}', function ($id) {
            return redirect()->route('admin.objects.edit', ['id' => $id]);
        })->name('object.edit');

        Route::get('/add', function () {
            return redirect()->route('admin.objects.create');
        })->name('object.create');

        Route::get('/', function () {
            return redirect()->route('admin.objects.index');
        })->name('object');
    });
