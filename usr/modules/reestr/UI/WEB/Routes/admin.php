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
use Reestr\UI\WEB\Controllers\ReestrController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/reestr')
    ->name('admin.')
    ->group(function () {
        Route::get('/getOrgs', [ReestrController::class,'getOrgs'])->name('reestr.getOrgs');
        Route::get('/getUsers', [ReestrController::class,'getUsers'])->name('reestr.getUsers');
        Route::get('/getObj', [ReestrController::class,'getObj'])->name('reestr.getObj');
        Route::get('/types', [ReestrController::class,'types'])->name('reestr.types');

        Route::resource('/', ReestrController::class)->names('reestr')->parameters(['' => 'id']);
    });
