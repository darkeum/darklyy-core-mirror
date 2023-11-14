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
use Scada\UI\WEB\Controllers\ScadaController;
use Scada\UI\WEB\Controllers\ScadaTagsController;
use Scada\UI\WEB\Controllers\ScadaActionsController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/scada')
    ->name('admin.')
    ->group(function () {
        Route::get("dashboard", [ScadaController::class, "dashboard"])->name('scada.dashboard');
        Route::resource("tags", ScadaTagsController::class)->names("scada.tags")
            ->parameters(['' => 'id'])
            ->except(['show']);
        Route::resource("actions", ScadaActionsController::class)->names("scada.actions")
            ->parameters(['' => 'id'])
            ->only(['index']);
        Route::resource("crash", ScadaCrashController::class)->names("scada.crash")
            ->parameters(['' => 'id'])
            ->only(['index']);
        Route::resource("/", ScadaController::class)->names('scada')
            ->parameters(['' => 'id'])
            ->except(['destroy']);
    });