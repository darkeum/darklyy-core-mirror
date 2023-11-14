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
use Promotions\UI\API\Controllers\PromotionsController;

Route::name('promotions.')->middleware('auth:sanctum')->prefix('/promotions')->group(
    function () {
        Route::post('/order',  [PromotionsController::class, 'order'])->name('order');          
        Route::get('/',  [PromotionsController::class, 'index'])->name('index');        
        Route::get('/{id}',  [PromotionsController::class, 'show'])->name('show');   
    }
);


