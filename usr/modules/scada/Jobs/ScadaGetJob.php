<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Jobs;

use Exception;
use Scada\Scada;
use Boot\Support\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Boot\Support\Facades\Cache;
use Boot\System\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScadaGetJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle()
    {
        try {
            Scada::collectScada($this->id);
            Cache::put('scada.work.key', 1, 60);
            Cache::put('scada.work.key.last', Carbon::now()->format('d.m.Y H:i:s'));
        } catch (Exception $exception) {
            
        }
    }
}
