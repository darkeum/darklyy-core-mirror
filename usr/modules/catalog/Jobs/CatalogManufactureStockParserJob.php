<?php
 
namespace Catalog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
 
class CatalogManufactureStockParserJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    protected $catalog_manufacture_stock_parser;

    public function __construct(\Catalog\Model\CatalogManufactureStockParser $catalog_manufacture_stock_parser){
        $this->catalog_manufacture_stock_parser = $catalog_manufacture_stock_parser;
    }
 
    public function handle(){
        if($this->catalog_manufacture_stock_parser->work == 0){
            $class = "\\".$this->catalog_manufacture_stock_parser->model_type;
            $class::parse();
        }
    }
}