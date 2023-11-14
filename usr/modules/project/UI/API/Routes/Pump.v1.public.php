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
use Project\UI\API\Controllers\Calculation\PumpCalculationController;

Route::middleware(['auth:sanctum'])->name('project.calculation.pump.')->prefix('/project/calculation/pump')->group(
    function () {
        Route::get('/search',  [PumpCalculationController::class, 'search'])->name('search');      
        Route::get('/models/search',  [PumpCalculationController::class, 'models_search'])->name('models.search');      
        Route::get('/models/{manufacture}',  [PumpCalculationController::class, 'models'])->name('models');  
        Route::get('/manufacture',  [PumpCalculationController::class, 'manufacture'])->name('manufacture');        
        Route::post('/request',  [PumpCalculationController::class, 'request'])->name('request');        
        Route::post('/',  [PumpCalculationController::class, 'calculate'])->name('calculate');          
        Route::post('/graph/{id}',  [PumpCalculationController::class, 'graph'])->name('graph');          
    }
);