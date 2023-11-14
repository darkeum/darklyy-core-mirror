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

class PumpModelsRequest extends Request
{
    public function rules(): array
    {
        return [                 
            'manufacture'       => 'required|min:0',         
        ];
    }

    public function messages(): array
    {
        return [                 
            'manufacture:required'          => Translation::trans('calculation.valve.validation.consumption.required'),       
        ];
    }
}