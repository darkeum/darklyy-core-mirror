<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Jobs;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Price\UI\CLI\PriceDaemon;
use Boot\System\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceGetJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function handle()
    {
        try {
            $PriceDaemon = new PriceDaemon;
            $PriceDaemon->start_by_ids($this->ids);
        } catch (Exception $exception) {
            
        }
    }
}
