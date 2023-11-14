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
use Service\UI\WEB\Controllers\PublicController;

// service
Route::mount('service', function () {

    Route::get('/', [PublicController::class, 'index'])->name('index');

    // complaints
    Route::mount('complaints', function () {
        Route::get('/', [PublicController::class, 'complaints'])->name('index');
        Route::post('send', [PublicController::class, 'complaints_send'])->name('send');
        Route::get('template', [PublicController::class, 'template'])->name('template');
    }, 'complaints.');

    // other
    Route::mount('order', function () {
        Route::get('/', [PublicController::class, 'order'])->name('index');
        Route::post('send/service', [PublicController::class, 'service'])->name('send');
    }, 'order.');

    // product
    Route::mount('product', function () {

        Route::get('{route}', [PublicController::class, 'product']);
        //Route::get('wilo', [PublicController::class, 'product'])->name('wilo');
        //Route::get('ridan', [PublicController::class, 'product'])->name('ridan');
        //Route::get('danfoss', [PublicController::class, 'product'])->name('danfoss');
        //Route::get('thermotronic', [PublicController::class, 'product'])->name('thermotronic');
        //Route::get('other', [PublicController::class, 'product'])->name('other');

    }, 'product.');

}, 'service.');
