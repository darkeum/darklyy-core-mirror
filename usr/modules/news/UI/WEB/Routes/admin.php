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
use News\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/news')
    ->name('admin.')
    ->group(function () {

        // Остальные
        Route::resource('/', ManagerController::class)->names('news')->parameters(['' => 'id']);

        //Route::resource('/',  ManagerController::class)->names('news');

    });
