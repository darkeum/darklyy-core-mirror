<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
*/

use \Boot\Support\Facades\Route;
use Feedback\UI\WEB\Controllers\PublicController;
use Feedback\UI\WEB\Controllers\AjaxController;

Route::mount('feedback', function ()  {

    Route::get('/',  [PublicController::class, 'index'])->name('index');

    //Route::mount('ajax', function ()  {
    //    Route::post('send',  [AjaxController::class, 'send'])->name('send');
    //}, 'ajax.');

}, 'feedback.');
