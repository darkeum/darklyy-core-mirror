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
use Scada\UI\API\Controllers\ScadaController;
use Scada\UI\API\Controllers\ScadaClientController;

Route::name('scada.')->prefix('/scada')->middleware('auth:sanctum')->group(
  function () {
    Route::get('/',  [ScadaController::class, 'index'])->name('index');
    Route::post('/action',  [ScadaController::class, 'action'])->name('action');
    Route::get('/tags/{id}',  [ScadaController::class, 'tags'])->name('tags');
    Route::get('/{id}',  [ScadaController::class, 'view'])->name('view');
  }
);

