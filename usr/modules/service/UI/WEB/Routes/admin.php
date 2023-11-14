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
use Service\UI\WEB\Controllers\ManagerController;


 Route::mount('/administration/module/service', function (){

     Route::resource('/',  ManagerController::class)->names('service');

 }, 'admin.');

// Router::dumpAlias();
