<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Valve Calculation Api Controller
*/

namespace Project\UI\API\Controllers\Calculation;

use Boot\System\Core\Abstracts\Controllers\ApiController;
use Project\UI\API\Requests\Calculation\PumpStationCalculationRequest;
use Project\Actions\Calculation\PumpStation\ApiPumpStationCalculationAction;
use Project\Actions\Calculation\PumpStation\ApiPumpStationRequestAction;
use Project\Actions\Calculation\PumpStation\ApiPumpStationManufactureAction;

class PumpStationCalculationController extends ApiController
{
    public function calculate(PumpStationCalculationRequest $request)
    {
        return app(ApiPumpStationCalculationAction::class)->run($request);
    }

    public function request(PumpStationCalculationRequest $request)
    {
        return app(ApiPumpStationRequestAction::class)->run($request);
    }

    public function manufacture()
    {
        return app(ApiPumpStationManufactureAction::class)->run();
    }
}
