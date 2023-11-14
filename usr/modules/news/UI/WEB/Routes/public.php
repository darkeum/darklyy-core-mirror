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
use News\UI\WEB\Controllers\PublicController;

Route::mount('news', function () {

    Route::get('/',  [PublicController::class, 'index'])->name('index');
    Route::get('tags/{tag}', [PublicController::class, 'tags'])->name('tags');
    Route::get('date/{date}', [PublicController::class, 'date'])->name('date');
    Route::get('{categories}/view/{post}', [PublicController::class, 'show'])->name('show');
    Route::get('{categories}', [PublicController::class, 'index'])->name('categories');

}, 'news.');


