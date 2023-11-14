<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (14.03.2023 21:46)
*/

use Boot\Support\Facades\Route;

use Controls\UI\WEB\Controllers\Controls;
use Controls\UI\WEB\Controllers\ControlsGod;
use Controls\UI\WEB\Controllers\ControlsPay;
use Controls\UI\WEB\Controllers\ControlsStorage;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/controls')
    ->name('admin.controls.')
    ->group(function () {

        #Контроль закупок (все)
        Route::resource('all', Controls::class)
            ->parameter('', 'id')
            ->names('all')
            ->middleware(['permission:control admin']);

        #Контроль остатков
        Route::prefix('storage')
            ->group(function () {

                //Route::post('storage/{id}', [ControlsStorage::class, 'update'])
                //    ->name('controls_update'); // Я хз что она делала ... Проверить

                Route::resource('/', ControlsStorage::class)
                    ->parameter('', 'id')
                    ->names('storage');
            })->middleware(['permission:control storage']);

        #Заявки на оплату
        Route::resource('pay', ControlsPay::class)
            ->parameter('', 'id')
            ->names('pay')->middleware(['permission:control pay']);

        #Управление остатками
        Route::get('god', [ControlsGod::class, 'index'])->name('god')->middleware(['permission:control god']);

        #Контроль закупок
        Route::resource('/', Controls::class)
            ->parameter('', 'id')->middleware(['permission:control supply']);

    });
