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
use Verifications\UI\WEB\Controllers\VerificationsController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/verifications')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [VerificationsController::class, 'index'])->name('verifications');
        Route::get('/create', [VerificationsController::class, 'create'])->name('verifications.create');
        Route::post('/creating', [VerificationsController::class, 'creating'])->name('verifications.creating');
        Route::get('/create/doc', [VerificationsController::class, 'createDoc'])->name('verifications.create.doc');
        Route::post('/create/doc/creating', [VerificationsController::class, 'creatingDoc'])->name('verifications.create.doc.creating');
        Route::get('/device', [VerificationsController::class, 'device'])->name('verifications.device');
        Route::get('/search', [VerificationsController::class, 'search'])->name('verifications.search');
        Route::get('/report', [VerificationsController::class, 'report'])->name('verifications.report');
    });
    