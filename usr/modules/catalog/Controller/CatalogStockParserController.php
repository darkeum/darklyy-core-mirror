<?php

namespace Catalog\Controller;

use \Boot\System\Http\Request;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;

class CatalogStockParserController extends AbstractController{
    static function index(){
        $grid = new \Catalog\Grid\CatalogManufactureStockParserGrid;
        display("@catalog/manager/stock/index.html.twig", [
            'grid' =>  $grid->render(),
        ]);
    }

    static function start_job($id){
        $catalog_manufacture_stock_parser = \Catalog\Model\CatalogManufactureStockParser::findOrFail($id);
        \Catalog\Jobs\CatalogManufactureStockParserJob::dispatch($catalog_manufacture_stock_parser);
        return response($catalog_manufacture_stock_parser);
    }
}