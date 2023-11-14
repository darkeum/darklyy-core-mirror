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
use Orgs\UI\WEB\Controllers\OrgsController;
use Orgs\UI\WEB\Controllers\SignsController;

Route::middleware(['theme:admin-material', 'permission:portal access'])
    ->prefix('/administration/module/orgs')
    ->name('admin.')
    ->group(function () {
        Route::resource('/sign', SignsController::class)->names('orgs.sign')->parameters(['' => 'id'])->except([
            'destroy'
        ]);
        Route::resource('/', OrgsController::class)->names('orgs')->parameters(['' => 'id']);
    });


