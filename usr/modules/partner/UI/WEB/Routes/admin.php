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
use Partner\UI\WEB\Controllers\ManagerController;

Route::mount('/administration/module/partner', function (){

    Route::resource('/',  ManagerController::class)->names('partner');

}, 'admin.');