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
use Catalog\UI\WEB\Controllers\PumpControl;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/catalog')
    ->name('admin.')
    ->group(function () {
        Route::get('/pump/control',  [PumpControl::class, "index"])->name("catalog.pump.control");
        Route::get('/pump/equate',  [PumpControl::class, "equate"])->name("catalog.pump.equate");
    });