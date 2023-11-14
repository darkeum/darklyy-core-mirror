<?php

use Boot\Support\Facades\Route;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->name('admin.')
    ->group(function () {

        // task
        Route::get('task/{id}', function ($id) {
            return redirect('administration/module/task/' . $id);
        })->name('task');


        // sales
        Route::get('sale/{id}', function ($id) {
            return redirect('administration/module/sales2/' . $id);
        })->name('sale');

    });