<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Boot\System\Bus\Dispatchable;
use Boot\System\Office\Converter;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class PdfJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file_url;
    private $file_name;

    public function __construct($file_url, $file_name)
    {
        $this->file_url = $file_url;
        $this->file_name = $file_name;
    }

    public function handle()
    {
        $converter = new Converter($this->file_url);
        $converter->convertTo($this->file_name);
    }

    // public function middleware()
    // {
    //     return [new WithoutOverlapping(0)];
    // }
}
