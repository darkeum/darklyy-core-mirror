<?php

use \Boot\Support\Facades\Router;
use \Boot\Services\Request;
use \Control\Controller\ControlController;
use \Control\Controller\ControlAllController;
use \Control\Controller\ControlDetailController;
use \Control\Controller\ControlPayController;
use \Control\Controller\ControlPayAllController;

Router::mount('/administration/module/control', function (){ 
    Router::resource('/pay_all', ControlPayAllController::class)->names("control_pay_all"); 
    Router::resource('/pay', ControlPayController::class)->names("control_pay"); 
    Router::resource('/control_detail', ControlDetailController::class)->names("control_detail"); 
    Router::post('/end/{id}', [ControlController::class, "end"])->name("control.end");
    Router::resource('/all', ControlAllController::class)->names("control_all"); 
    Router::resource('/', ControlController::class)->names("control"); 
}, "admin.");

// dd(Router::dumpAlias());