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
use \Project\Controller\PublicController;
use \Project\Controller\PublicCalculationController;
use \Project\Controller\PublicToolController;
use \Project\Controller\PublicDocumentsController;
use \Project\Controller\SendCalculationController;
use \Project\Controller\SendToolController;
use \Project\Controller\SendScalesController;
use \Project\Controller\PublicMeteringController;
use \Project\Controller\MeteringCalculationController;

Router::mount('/project', function () {
    Router::get('/',  [PublicController::class, 'index'])->name('index');  
    Router::get('/order',  [PublicController::class, 'order'])->name('order');     
    Router::mount('/calculation', function () {
        Router::get('/',  [PublicCalculationController::class, 'index'])->name('index');  
        Router::get('/heat', [PublicCalculationController::class, 'heat'])->name('heat'); 
        Router::post('/heat/send', [PublicCalculationController::class, 'send'])->name('heat.send'); 
        Router::get('/pump', [PublicCalculationController::class, 'pump'])->name('pump'); 
        Router::get('/pump_station', [PublicCalculationController::class, 'pump_station'])->name('pump_station'); 
        Router::get('/valve', [PublicCalculationController::class, 'valve'])->name('valve'); 
        Router::get('/block', [PublicCalculationController::class, 'block'])->name('block'); 
        Router::mount('/metering_station', function () {
            Router::get('/', [PublicCalculationController::class, 'metering_station'])->name('metering_station'); 
            Router::all('/test', [PublicMeteringController::class, 'test'])->name('metering_station.test'); 
        });        
        Router::mount('/send', function () {
            Router::post('/heat', [SendCalculationController::class, 'heat'])->name('heat'); 
            Router::post('/valve', [SendCalculationController::class, 'valve'])->name('valve'); 
            Router::post('/block', [SendCalculationController::class, 'block'])->name('block'); 
            Router::post('/pump', [SendCalculationController::class, 'pump'])->name('pump'); 
            Router::post('/pump_station', [SendCalculationController::class, 'pump_station'])->name('pump_station'); 
            Router::post('/metering_station', [SendCalculationController::class, 'metering_station'])->name('metering_station');          
        }, 'send.');   
        Router::mount('/calc', function () {
            Router::post('/metering_station', [MeteringCalculationController::class, 'metering_station'])->name('metering_station');          
        }, 'calc.');      
    }, 'calculation.');

    Router::mount('/tool', function () {
        Router::get('/',  [PublicToolController::class, 'index'])->name('index');  
        Router::get('/pipes',  [PublicToolController::class, 'pipes'])->name('pipes');  
        Router::get('/power',  [PublicToolController::class, 'power'])->name('power');  
        Router::get('/scales',  [PublicToolController::class, 'scales'])->name('scales');  
        Router::mount('/convert', function () {
            Router::post('/pipes',  [SendToolController::class, 'pipes'])->name('pipes');      
            Router::all('/scales',  [SendScalesController::class, 'scales'])->name('scales');      
            Router::get('/scales_download',  [SendScalesController::class, 'scales_download'])->name('scales_download');      
        }, 'convert.');    
    }, 'tool.');   

    Router::mount('/documents', function () {
        Router::get('/gost', function () {})->name('gost');  
        Router::get('/snip',  function () {})->name('snip');   
    }, 'documents.');
}, 'project.');

Router::mount('/administration/module/project', function (){ 
    Router::post('/blocks/parse', [\Project\Controller\BlocksController::class, 'parse'])->name("blocks.parse");
    Router::get('/blocks/download', [\Project\Controller\BlocksController::class, "download"])->name("blocks.download");
    Router::resource('/blocks', \Project\Controller\BlocksController::class)->names("blocks");
}, "admin.project.");

