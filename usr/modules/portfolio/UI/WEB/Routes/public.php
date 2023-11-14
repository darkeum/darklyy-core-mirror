<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
*/

use Boot\Support\Facades\Route;
use Portfolio\UI\WEB\Controllers\PublicController;

Route::mount('portfolio', function () {

    Route::get('/',  [PublicController::class, 'index'])->name('index');
    Route::get('{categories}/view/{post}', [PublicController::class, 'show'])->name('show');
    Route::get('{categories}', [PublicController::class, 'index'])->name('categories');

}, 'portfolio.');


