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
use Project\UI\API\Controllers\Calculation\PumpStationCalculationController;


Route::middleware(['auth:sanctum'])->name('project.calculation.pump_station.')->prefix('/project/calculation/pump_station')->group(
    function () {
        // Route::get('/models/search',  [PumpCalculationController::class, 'models_search'])->name('models.search');      
        // Route::get('/models/{manufacture}',  [PumpCalculationController::class, 'models'])->name('models');  
        Route::get('/manufacture',  [PumpStationCalculationController::class, 'manufacture'])->name('manufacture');        
        Route::post('/request',  [PumpStationCalculationController::class, 'request'])->name('request');        
        Route::post('/',  [PumpStationCalculationController::class, 'calculate'])->name('calculate');          
        // Route::get('/graph/{id}',  [PumpCalculationController::class, 'graph'])->name('graph');          
    }
);


