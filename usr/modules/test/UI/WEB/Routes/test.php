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
use \Test\UI\WEB\Controllers\TestController;

Route::mount('/administration/module/test', function (){

    Route::get('/boiler_add',  [TestController::class, 'boiler_add'])->name('test');

}, 'admin.');
