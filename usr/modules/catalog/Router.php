<?php

use Boot\System\Http\Request;
use Boot\Support\Facades\Hash;
use \Boot\Support\Facades\Route;
use \Boot\Support\Facades\Router;
use \Catalog\Controller\CatalogExcelController;
use \Catalog\Controller\CatalogAttributeController;
use \Catalog\Controller\CatalogAttributeValueController;
use \Catalog\Controller\CatalogAttributeCategoryController;

Route::prefix('/administration/module/catalog')->name("admin.")->group(function (){ 
    // Router::get('/excel',  [CatalogExcelController::class, "editContractsList"])->name("organizations.editContractsList");
 
    Route::post('/excel/batch_list',  [CatalogExcelController::class, "batch_list"])->name("catalog.excel.batch_list");
    Route::post('/excel/download',  [CatalogExcelController::class, "download"])->name("catalog.excel.download");
    Route::post('/excel/get_count',  [CatalogExcelController::class, "get_count"])->name("catalog.excel.get_count");
    Route::post('/excel/unload',  [CatalogExcelController::class, "unload"])->name("catalog.excel.unload");
    Route::get('/excel/get_file',  [CatalogExcelController::class, "get_file"])->name("catalog.excel.get_file");
    Route::get('/excel/files',  [CatalogExcelController::class, "files"])->name("catalog.excel.files");
    Route::post('/excel/cancel_batch',  [CatalogExcelController::class, "cancel_batch"])->name("catalog.excel.cancel_batch");
    Route::get('/excel',  [CatalogExcelController::class, "index"])->name("catalog.excel.index");

    Route::resource('/attribute/value',  CatalogAttributeValueController::class)->names("catalog.attribute.value");
    Route::post('/attribute/value/mass_update',  [CatalogAttributeValueController::class, "mass_update"])->name("catalog.attribute.value.mass_update");
    Route::post('/attribute/category/mass_update',  [CatalogAttributeCategoryController::class, "mass_update"])->name("catalog.attribute.category.mass_update");
    Route::get('/attribute/get/{id}',  [CatalogAttributeController::class, "get"])->name("catalog.attribute.get");
    Route::get('/attribute/get_values/{id}',  [CatalogAttributeController::class, "get_values"])->name("catalog.attribute.get_values");
    Route::get('/attribute/load/{category_id}',  [CatalogAttributeController::class, "load"])->name("catalog.attribute.load");
    Route::get('/attribute/load_all',  [CatalogAttributeController::class, "load_all"])->name("catalog.attribute.load_all");
    Route::get('/attribute/search',  [CatalogAttributeController::class, "search"])->name("catalog.attribute.search");
    Route::get('/attribute/all',  [CatalogAttributeController::class, "all"])->name("catalog.attribute.all");
    Route::resource('/attribute',  CatalogAttributeController::class)->names("catalog.attribute");

    Route::post('/get_attributes', [\Catalog\Controller\CatalogController::class, "get_attributes"])->name("get_attributes"); 
    Route::post('/sku_generate', [\Catalog\Controller\CatalogController::class, "sku_generate"])->name("sku_generate"); 
    Route::get('/stock_parser/start_job/{id}', [\Catalog\Controller\CatalogStockParserController::class, "start_job"])->name("stock_parser.start_job"); 

    Route::resource('/stock_parser', \Catalog\Controller\CatalogStockParserController::class)->names("catalog.stock_parser");
    Route::resource('/', \Catalog\Controller\CatalogController::class)->names("catalog")->parameters(['' => 'id']);


});



// dd(Router::dumpAlias());