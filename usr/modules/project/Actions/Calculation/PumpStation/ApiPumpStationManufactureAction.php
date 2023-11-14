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

namespace Project\Actions\Calculation\PumpStation;

use Boot\Support\Facades\Config;
use Boot\Abstracts\Actions\Action;
use Project\Tasks\Calculation\Pump\PumpOptionsTask;

class ApiPumpStationManufactureAction extends Action
{
    public function run(): array
    {
        $options = app(PumpOptionsTask::class)->run(true);
        return ['manufacture' => $options['manufacture']];
    }
}
