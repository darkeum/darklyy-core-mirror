<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

use Boot\Support\Facades\Route;
use Engineerings\UI\WEB\Controllers\EngineeringsController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/engineerings')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [EngineeringsController::class, 'index'])->name('engineerings');
        
        Route::get('/register', [EngineeringsController::class, 'register'])->name('engineerings.register');
        Route::get('/register/create', [EngineeringsController::class, 'registerCreate'])->name('engineerings.register.create');
        Route::post('/register/create/creating', [EngineeringsController::class, 'registerCreating'])->name('engineerings.register.creating');
        
       
    });
    