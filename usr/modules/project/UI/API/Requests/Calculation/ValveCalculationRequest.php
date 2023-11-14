<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Valve Calculation Api Request
*/

namespace Project\UI\API\Requests\Calculation;

use \Boot\Abstracts\Requests\Request;
use \Boot\Support\Facades\Translation;

class ValveCalculationRequest extends Request
{
    public function rules(): array
    {
        return [                 
            'consumption'       => 'required|min:0',
            'pressure_drop'     => 'required_if:cavitation_check,1,on,yes,true',
            'pressure_before'   => 'required_if:cavitation_check,1,on,yes,true',
            'pressure_after'    => 'required_if:cavitation_check,1,on,yes,true',           
            't'                 => 'required_if:cavitation_check,1,on,yes,true',           
            'system'            => 'required|in:gvs,heat,venting',           
            'type'              => 'required|in:2,3',           
            'cavitation_check'  => 'required|boolean',           
        ];
    }

    public function messages(): array
    {
        return [                 
            'consumption:required'          => Translation::trans('calculation.valve.validation.consumption.required'),      
            'pressure_drop:required_if'     => Translation::trans('calculation.valve.validation.pressure_drop.required_if'),    
            'pressure_before:required_if'   => Translation::trans('calculation.valve.validation.pressure_before.required_if'),    
            'pressure_after:required_if'    => Translation::trans('calculation.valve.validation.pressure_after.required_if'),    
            't:required_if'                 => Translation::trans('calculation.valve.validation.t.required_if'),    
            'system:required'               => Translation::trans('calculation.valve.validation.system.required'),    
            'system:in'                     => Translation::trans('calculation.valve.validation.system.in'),    
            'type:required'                 => Translation::trans('calculation.valve.validation.type.required'),       
            'type:in'                       => Translation::trans('calculation.valve.validation.type.in'),    
            'cavitation_check:required'     => Translation::trans('calculation.valve.validation.cavitation_check.required'),    
            'cavitation_check:boolean'      => Translation::trans('calculation.valve.validation.cavitation_check.boolean'),    
        ];
    }
}