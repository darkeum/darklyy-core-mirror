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

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Boot\Models\Attach;
 
class AttachJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    public function __construct(){
        $this->onQueue('attach');
    }
 
    public function handle(){
        foreach(Attach::where("daemon", 0)->whereNotNull("class_name")->lazy() as $attach){
            $className = $attach->class_name;
            if(class_exists($className)){
                $cn = new $className;
                $cn->local_file = ROOT.$attach->url;
                $cn->remote_file = (env('APP_NAME') != 'AE' ? env('APP_NAME').'/' : '').$attach->url;
                $cn->put_file();

                $attach->daemon = 1;
                $attach->save();
            }
        }
    }
}