<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
*/

use Boot\Support\Facades\Route;
use Promotions\UI\API\Controllers\PromotionsController;
use Promotions\UI\WEB\Controllers\ManagerPromotionsController;


Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/promotions')
    ->name('admin.')
    ->group(function () {

        Route::get('/getSales', [ManagerPromotionsController::class, 'getSales'])->name('promotions.sales');        

        Route::resource('/', ManagerPromotionsController::class)->names('promotions')->parameters(['' => 'id']);
    });
