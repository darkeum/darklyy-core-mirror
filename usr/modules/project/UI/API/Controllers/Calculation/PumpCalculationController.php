<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\UI\API\Controllers\Calculation;

use Boot\System\Http\Request;
use Project\Actions\Calculation\Pump\ApiPumpGraphAction;
use Boot\System\Core\Abstracts\Controllers\ApiController;
use Project\Actions\Calculation\Pump\ApiPumpModelsAction;
use Project\Actions\Calculation\Pump\ApiPumpSearchAction;
use Project\UI\API\Requests\Calculation\PumpGraphRequest;
use Project\Actions\Calculation\Pump\ApiPumpRequestAction;
use Project\Actions\Calculation\Pump\ApiPumpCalculationAction;
use Project\Actions\Calculation\Pump\ApiPumpManufactureAction;
use Project\Actions\Calculation\Pump\ApiPumpModelsSearchAction;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;
use Project\UI\API\Requests\Calculation\PumpModelsSearchRequest;

class PumpCalculationController extends ApiController
{

    public function search(Request $request)
    {
        return app(ApiPumpSearchAction::class)->run($request);
    }

    public function manufacture()
    {
        return app(ApiPumpManufactureAction::class)->run();
    }

    public function models($manufacture)
    {
        return app(ApiPumpModelsAction::class)->run($manufacture);
    }

    public function models_search(PumpModelsSearchRequest $request)
    {
        return app(ApiPumpModelsSearchAction::class)->run($request);
    }

    public function calculate(PumpCalculationRequest $request)
    {
        return app(ApiPumpCalculationAction::class)->run($request);
    }

    public function request(PumpCalculationRequest $request)
    {
        return app(ApiPumpRequestAction::class)->run($request);
    }

    public function graph(PumpGraphRequest $request)
    {
        return app(ApiPumpGraphAction::class)->run($request);
    }
}
