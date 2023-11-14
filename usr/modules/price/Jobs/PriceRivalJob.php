<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Jobs;

use Illuminate\Bus\Queueable;
use Price\UI\CLI\PriceDaemon;
use Price\Models\CatalogPrice;
use Boot\System\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceRivalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    public function __construct(){
        $this->onQueue('catalog_price_rival');
    }

    public function handle(){
        $PriceDaemon = new PriceDaemon;
        $PriceDaemon->prices_create();
        $query = $PriceDaemon->get_query();
        $query->where("daemon", 0)->chunk($PriceDaemon->limit, function($items) use (&$PriceDaemon){
            foreach($items as $item){
                $PriceDaemon->price_daemon_type($item);
                $item->update(["daemon" => 1]);
                // dump($item->id);
            }
        });
        $PriceDaemon->prices_daemon();
        CatalogPrice::where('work', 1)->update(['work' => 0]);
    }
}