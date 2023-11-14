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
use Integration\UI\WEB\Controllers\IntegrationController;
use Integration\UI\WEB\Controllers\MatchingController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/integration')
    ->name('admin.')
    ->group(function () {
        Route::get('matching/category', [MatchingController::class, 'category'])->name("integration.matching.category");
        Route::get('matching/attribute', [MatchingController::class, 'attribute'])->name("integration.matching.attribute");
        Route::get('matching/values', [MatchingController::class, 'values'])->name("integration.matching.values");
        Route::resource('/', IntegrationController::class)->names('integration')->only('index', 'show', 'create', 'edit')->parameters(['' => 'id']);
    });