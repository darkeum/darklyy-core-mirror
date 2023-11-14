<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Valve calculation API Router
*/

use Boot\Support\Facades\Route;
use Promotions\UI\API\Controllers\PromotionsController;


Route::name('promotions.')->prefix('/promotions')->group(
    function () {
        Route::get('/',  [PromotionsController::class, 'index'])->name('index');        
        Route::get('/{promotion}',  [PromotionsController::class, 'show'])->name('show');          
    }
);


