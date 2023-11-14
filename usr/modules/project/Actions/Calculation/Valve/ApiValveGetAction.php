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

namespace Project\Actions\Calculation\Valve;

use \Boot\Abstracts\Actions\Action;
use \Project\Tasks\Calculation\Valve\ValveGetTask;
use \Project\UI\API\Requests\Calculation\ValveGetRequest;

class ApiValveGetAction extends Action
{
    // public function run(ValveGetRequest $request): array
    // {
    //     $sanitizedData = $request->sanitizeInput([
    //         'consumption',
    //         'pressure_drop',
    //         'pressure_before',
    //         'pressure_after',
    //         't' => 150,
    //         'system' => 'heat',
    //         'type' => '2',
    //         'cavitation_check' => true
    //     ]);

    //     $task = (new ValveGetTask)->run($sanitizedData);
    //     return $task;
    // }
}