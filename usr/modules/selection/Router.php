<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
*/

use \Boot\Support\Facades\Router;
use \Selection\Controller\PublicController;
use \Selection\Controller\ManagerController;

Router::mount('/selection', function () {
    Router::get('/',  [PublicController::class, 'index'])->name('index');  
    Router::get('/construct/{id}',  [PublicController::class, 'construct'])->name('construct');  
    Router::get('/blockConstruct/{id}',  [PublicController::class, 'blockConstruct'])->name('blockConstruct');  
    Router::get('/scheme/333',  [PublicController::class, 'scheme'])->name('scheme');  
  
}, 'selection.');

// Router::mount('/administration/module/certificates', function (){ 
//     Router::resource('/',  ManagerController::class)->names('certificates');  
// }, 'admin.');
