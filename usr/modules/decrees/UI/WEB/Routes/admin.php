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
use Decrees\UI\WEB\Controllers\Controller;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/decrees')
    ->name('admin.')
    ->group(function () {
        Route::get('/doc_create/{id}/{type}', [Controller::class, 'doc_create'])
            ->name('decrees.doc_create');
        Route::get('/getFile/{id}/{mode}', [Controller::class, 'getFile'])
            ->name('decrees.getFile');
        Route::resource('/', Controller::class)->names('decrees')->parameters(['' => 'id']);
    });
