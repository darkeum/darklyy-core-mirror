<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Home WEB Router
*/

use Boot\Support\Facades\Route;
use Home\UI\WEB\Controllers\PublicController;

Route::get('/',  [PublicController::class, 'index'])->name('home');