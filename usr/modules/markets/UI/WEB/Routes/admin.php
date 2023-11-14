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
use Markets\UI\WEB\Controllers\OzonController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/markets')
    ->name('admin.markets.')
    ->group(function () {

        Route::prefix('ozon')
            ->name('ozon.')
            ->group(function () {
                Route::get('/createCard/{id}', [OzonController::class,'createCard'])->name('createCard');
            });


});
