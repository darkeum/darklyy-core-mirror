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
use Scada\Controller\PublicController;
use Scada\UI\WEB\Controllers\ScadaPublicController;

Route::mount('/scada', function () {
    Route::get('/',  [ScadaPublicController::class, 'index'])->name('index');
    Route::get('/ajax/{id}',  [ScadaPublicController::class, 'ajax'])->name('ajax');
    Route::get('/project/{id}',  [ScadaPublicController::class, 'project'])->name('project');
    Route::get('/chart/{id}',  [ScadaPublicController::class, 'chart'])->name('chart');
    Route::get('/variable',  [ScadaPublicController::class, 'variable'])->name('variable');
    Route::mount('/scada/action', function () {
        Route::get('/',  [ScadaPublicController::class, 'index'])->name('index');
        Route::get('/ajax/{id}',  [ScadaPublicController::class, 'ajax'])->name('ajax');
        Route::get('/project/{id}',  [ScadaPublicController::class, 'project'])->name('project');
        Route::get('/variable',  [ScadaPublicController::class, 'variable'])->name('variable');
    }, 'scada.');
  }, 'scada.');

