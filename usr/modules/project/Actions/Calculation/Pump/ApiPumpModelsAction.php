<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Valve Calculation Api Action 
*/

namespace Project\Actions\Calculation\Pump;

use Boot\Support\Facades\Storage;
use Boot\Abstracts\Actions\Action;
use Project\Tasks\Calculation\Pump\PumpOptionsTask;

class ApiPumpModelsAction extends Action
{
    public function run($manufacture): array
    {
        $options = app(PumpOptionsTask::class)->run();
        $models = [];
        foreach ($options['models'][$manufacture] as $value) {
            $models[] = $value;
        }
        return ['models' =>  $models];        
    }
}
