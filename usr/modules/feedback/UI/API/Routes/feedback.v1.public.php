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
use Feedback\UI\API\Controllers\FeedbackSendController;

Route::name('feedback.')->prefix('/feedback')->group(
    function () {
        Route::post('/send',  [FeedbackSendController::class, 'send'])->name('send');
    }
);


