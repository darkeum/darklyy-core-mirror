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
use Profile\UI\API\Controllers\ApiLoginController;

Route::prefix('profile')->group(function () {
    Route::get('/', [ApiLoginController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('/login', [ApiLoginController::class, 'login']);
});
