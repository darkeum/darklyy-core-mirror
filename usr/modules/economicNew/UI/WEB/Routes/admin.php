<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (28.09.2023 13:01)
*/


use Boot\Support\Facades\Route;
use EconomicNew\UI\WEB\Controllers\EconomicAct;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/economicNew')
    ->name('admin.economic_new.')
    ->group(function () {

        Route::resource('act', EconomicAct::class)
            ->parameter('', 'id')
            ->names('act');
    });