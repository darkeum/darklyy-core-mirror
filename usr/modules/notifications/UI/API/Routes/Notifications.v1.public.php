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
use Notifications\UI\API\Controllers\ApiNotificationsController;

Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/',  [ApiNotificationsController::class, 'index'])->name('index');
});
