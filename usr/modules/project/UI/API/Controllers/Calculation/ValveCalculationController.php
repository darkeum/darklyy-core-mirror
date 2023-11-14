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

use \Boot\Abstracts\Controllers\ApiController;
use \Project\Actions\Calculation\Valve\ApiValveCalculationAction;
use \Project\UI\API\Requests\Calculation\ValveCalculationRequest;
use \Project\UI\API\Transformers\Calculation\ValveCalculationTransformer;

class ValveCalculationController extends ApiController
{
    public function getCalculation()
    {
        $response = $this->json(array('sss'));
        return  $response;
    }

    public function calculate(ValveCalculationRequest $request)
    {
        $result = (new ApiValveCalculationAction)->run($request);
        return $result;
        // return $this->transform($result, ValveCalsculationTransformer::class);
    }
}
