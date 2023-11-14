<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

use Boot\Support\Facades\Route;
use Catalogv2s\UI\WEB\Controllers\Catalogv2sController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/catalogv2s')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Catalogv2sController::class, 'index'])->name('catalogv2s.index');

        Route::get('/pump', [Catalogv2sController::class, 'pumpIndex'])->name('catalogv2s.pump');
        Route::get('/pump/edit/{id}', [Catalogv2sController::class, 'pumpEdit'])->name('catalogv2s.pump.edit');
        Route::post('/pump/update/{id}', [Catalogv2sController::class, 'pumpUpdate'])->name('catalogv2s.pump.update');
        Route::delete('/pump/delete/{id}', [Catalogv2sController::class, 'pumpDestroy'])->name('catalogv2s.pump.destroy');
        Route::post('/pump/create', [Catalogv2sController::class, 'pumpCreate'])->name('catalogv2s.pump.create');
        
    });
