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
use Price\UI\WEB\Controllers\PriceController;
use Price\UI\WEB\Controllers\PriceTypeController;
use Price\UI\WEB\Controllers\PriceRivalController;
use Price\UI\WEB\Controllers\PriceSettingsController;
use Price\UI\WEB\Controllers\PriceReceivingController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/price')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [PriceController::class, 'index'])->name("price.index");
        Route::get('detail/{id}', [PriceController::class, 'show'])->name("price.show");
        Route::get('add/', [PriceController::class, 'add'])->name("price.add");
        Route::get('search/', [PriceController::class, 'search'])->name("price.search");

        Route::get('victoria_add/{id}', [PriceController::class, 'victoria'])->name("price.victoria");
        Route::post('victoria_item_add/{id}', [PriceController::class, 'victoria_add'])->name("price.victoria.add");
        Route::post('detail/save', [PriceController::class, 'detail_save'])->name("price.detail.save");
        Route::post('detail/delete', [PriceController::class, 'detail_delete'])->name("price.detail.delete");
       
        Route::get('receiving/', [PriceReceivingController::class, 'index'])->name("price.receiving");

        Route::post('save/', [PriceController::class, 'save'])->name("price.save");
        Route::post('comment/', [PriceController::class, 'comment'])->name("price.comment");
        
        Route::get('print/{id}', [PriceController::class, 'print'])->name("price.print");

        Route::get('settings/list', [PriceSettingsController::class, 'index'])->name("price.settings.list");

        Route::post('settings/save', [PriceSettingsController::class, 'save'])->name("price.settings.save");
        Route::post('settings/delete', [PriceSettingsController::class, 'delete'])->name("price.settings.delete");
        Route::post('settings/update', [PriceSettingsController::class, 'update'])->name("price.settings.update");
        Route::post('settings/get_disable_btn/{id}', [PriceSettingsController::class, 'get_disable_btn'])->name("price.settings.get_disable_btn");

        Route::get('settings/edit/{id}', [PriceSettingsController::class, 'edit'])->name("price.settings.edit");
        Route::get('settings/check/{id}', [PriceSettingsController::class, 'check'])->name("price.settings.check");
       
        Route::get('settings/load', [PriceSettingsController::class, 'load'])->name("price.settings.load");
        Route::post('settings/load/excel', [PriceSettingsController::class, 'excel'])->name("price.settings.load.excel");
        Route::post('settings/load/count', [PriceSettingsController::class, 'count'])->name("price.settings.load.count");
        Route::get('settings/load/template', [PriceSettingsController::class, 'template'])->name("price.settings.load.template");

        Route::get('settings/account', [PriceSettingsController::class, 'account'])->name("price.settings.account");
        Route::post('settings/account/change/password', [PriceSettingsController::class, 'password'])->name("price.settings.account.password");
        Route::post('settings/account/change/login', [PriceSettingsController::class, 'login'])->name("price.settings.account.login");

        Route::get('rival/client', [PriceRivalController::class, 'client'])->name("price.rival.client");
        Route::get('rival/client/edit/{id}', [PriceRivalController::class, 'edit'])->name("price.rival.client.edit");
        Route::post('rival/client/action', [PriceRivalController::class, 'action'])->name("price.rival.client.action");
        Route::post('rival/client/data', [PriceRivalController::class, 'data'])->name("price.rival.client.data");

        Route::get('rival/group', [PriceRivalController::class, 'group'])->name("price.rival.group");
        Route::get('rival/group/oid', [PriceRivalController::class, 'oid'])->name("price.rival.group.oid");
        Route::post('rival/group/list/{id}', [PriceRivalController::class, 'list'])->name("price.rival.group.list");
        Route::post('rival/group/set', [PriceRivalController::class, 'set'])->name("price.rival.group.set");

        Route::get('types/', [PriceTypeController::class, 'index'])->name("price.types.index");
        Route::get('types/edit/{id}', [PriceTypeController::class, 'edit'])->name("price.types.edit");
        Route::put('types/update', [PriceTypeController::class, 'update'])->name("price.types.update");
    });
