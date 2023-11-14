<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Home API Router
*/

use Boot\Support\Facades\Route;
use Home\UI\API\Controllers\ApiHomeController;

Route::prefix('home')->middleware('auth:sanctum')->group(function () {
    Route::get('/',  [ApiHomeController::class, 'dashboard'])->name('dashboard');
});
