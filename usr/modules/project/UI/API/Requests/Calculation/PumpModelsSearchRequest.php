<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Pump Calculation Api Request
*/

namespace Project\UI\API\Requests\Calculation;

use Boot\System\Core\Abstracts\Requests\Request;

class PumpModelsSearchRequest extends Request
{

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids'      => 'required',
        ];
    }

    
}
