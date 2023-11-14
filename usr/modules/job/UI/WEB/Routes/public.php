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
use Job\UI\WEB\Controllers\PublicController;

Route::mount('job', function () {

    Route::get('/',  [PublicController::class, 'index'])->name('index');

    Route::get('subscribe', [PublicController::class, 'subscribe']);
    Route::post('subscribe', [PublicController::class, 'actionSubscribe']);

    Route::get('send', [PublicController::class, 'send']);
    Route::post('send', [PublicController::class, 'actionSend']);

    Route::get('{categories}/view/{post}', [PublicController::class, 'show']);
    Route::get('{categories}', [PublicController::class, 'index']);
}, 'job.');
