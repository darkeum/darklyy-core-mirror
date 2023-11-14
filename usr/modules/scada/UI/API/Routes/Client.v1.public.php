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
use Scada\UI\API\Controllers\ScadaClientController;

Route::name('scada.')->prefix('/scada')->group(
  function () {
    Route::get('/client/api/settings',  [ScadaClientController::class, 'settings'])->name('client.settings');
  }
);

Route::name('scada.')->prefix('/scada')->group(
  function () {
    Route::get('/socket',  [ScadaClientController::class, 'settings'])->name('client.settings');
  }
);

