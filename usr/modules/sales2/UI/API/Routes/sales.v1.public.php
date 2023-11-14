<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Scada API Router
*/

use Boot\Support\Facades\Route;
use Sales2\UI\API\Controllers\PrintApiController;

Route::middleware(['auth:sanctum'])
    ->prefix('/sales')
    ->name('sales.')
    ->group(function () {
        Route::get('/print/{type}/{id}',  [PrintApiController::class, "print"])->name("print");
    });



