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
use Page\UI\WEB\Controllers\PublicController;

Route::mount('page', function () {

    Route::get('{route}', [PublicController::class, 'index']);

}, 'page.');
