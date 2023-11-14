<?php

use Boot\Support\Facades\Route;
use Slider\UI\WEB\Controllers\PublicController;

Route::mount('slider', function () {

//    Route::get('/',  [PublicController::class, 'index'])->name('index');
    //Route::get('tags/{tag}', [PublicController::class, 'tags'])->name('tags');
    //Route::get('date/{date}', [PublicController::class, 'date'])->name('date');
    //Route::get('{categories}/view/{post}', [PublicController::class, 'show'])->name('show');
    //Route::get('{categories}', [PublicController::class, 'index'])->name('categories');

}, 'slider.');


