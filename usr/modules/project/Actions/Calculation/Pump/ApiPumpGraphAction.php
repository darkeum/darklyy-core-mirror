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

use Boot\Abstracts\Actions\Action;
use Project\Tasks\Calculation\Pump\PumpGraphTask;
use Project\UI\API\Requests\Calculation\PumpGraphRequest;


class ApiPumpGraphAction extends Action
{
    /**
     * Обработчик действия
     *
     * @param  mixed $request
     * @return array
     */
    public function run(PumpGraphRequest $request)
    {
        $data = $request->sanitizeInput([
            'id',
            'q',
            'h',
            'priority' => 1,
            'work_mark' => 'A1',
            'directory' => null,
			'pump_switch' => 0,
			'pump_count' => 1,			
			'q_all',
			'pump_reserve' => 0,
			'pump_reserve_count',
        ]);
        app(PumpGraphTask::class)->run($data);
    }
}
