<?php

use \Boot\Support\Facades\Router;
use \Boot\Services\Request;
use \Victoria\Controller\VictoriaController;

Router::mount('/administration/module/victoria', function (){ 

    Router::all('/',  [VictoriaController::class, "index"])->name("victoria.index"); 
    Router::all('/load_list',  [VictoriaController::class, "load_list"])->name("victoria.load_list"); 
    Router::all('/get_item',  [VictoriaController::class, "get_item"])->name("victoria.get_item"); 
    Router::all('/filter',  [VictoriaController::class, "filter"])->name("victoria.filter"); 
    Router::all('/cert',  [VictoriaController::class, "cert"])->name("victoria.cert"); 
    Router::all('/attribute',  [VictoriaController::class, "attribute"])->name("victoria.attribute"); 

}, "admin.");

// dd(Router::dumpAlias());