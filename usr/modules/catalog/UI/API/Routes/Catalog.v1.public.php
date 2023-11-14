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
use Catalog\UI\API\Controllers\CatalogController;

Route::name('catalog.')->middleware('auth:sanctum')->prefix('/catalog')->group(
    function () {   
        Route::get('/list',  [CatalogController::class, 'list'])->name('list');        
        Route::get('/{id}',  [CatalogController::class, 'show'])->name('show');   
    }
);


