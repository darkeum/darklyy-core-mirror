<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (03.07.2023 16:10)
*/

// Роуты для работы с детальной страницей 'Заявки на оплату'
use Boot\Support\Facades\Route;
use Controls\UI\WEB\Controllers\Controls;
use Controls\UI\WEB\Controllers\ControlsDetailPay;
use Controls\UI\WEB\Controllers\ControlsGod;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/detail_pay')
    ->name('admin.controls.')
    ->controller(ControlsDetailPay::class)
    ->group(function () {
        Route::post('edit_comment/{id}', 'editComment')
            ->name('detail_edit');
        Route::post('edit_price/{id}', 'editPrice')
            ->name('edit_price');
        Route::post('save_date/{id}', 'saveDate')
            ->name('save_date');
        Route::post('delete_date/{id}', 'deletePayDate')
            ->name('delete_date');
        Route::post('date_pay/{id}', 'datePay')
            ->name('DatePay');
        Route::post('end/{id}', [Controls::class, 'end'])
            ->name('controls_end');
    });

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/controls')
    ->name('admin.controls.')
    ->controller(ControlsGod::class)
    ->group(function () {
        Route::post('search', 'search')
            ->name('search');
        Route::post('delete_list', 'delete')
            ->name('delete_list');
    });

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('administration/module/controls')
    ->name('admin.controls.')
    ->controller(Controls::class)
    ->group(function () {
        Route::post('update_date_pay/{id}', 'updateDatePay')
            ->name('updateDatePay');
    });