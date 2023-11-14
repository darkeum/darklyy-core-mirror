<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2023 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Kirill Gusev
*/

use \Boot\Support\Facades\Route;
use \Reviews\UI\WEB\Controllers\ManagerController;


Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/reviews/')
    ->name('admin.')
    ->group(function () {
        Route::get('/getOrgs', [ManagerController::class,'getOrgs'])->name('reviews.getOrgs');

        Route::resource('/',  ManagerController::class)->names('reviews')->parameters(['' => 'id']);
    });

