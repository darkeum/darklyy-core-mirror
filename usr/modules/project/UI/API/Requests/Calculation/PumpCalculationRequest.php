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

class PumpCalculationRequest extends Request
{

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'pumpSelector'      => 'required',
            'q'                 => 'required|numeric|between:0,15000',
            'h'                 => 'required|numeric|between:0,15000',
            'oid'               => 'required|exists:\Organizations\Model\Organizations,id',
            // 'object_id'         => 'required|exists:\Object\Model\ObjectModel,id',
            // 'manufacture'       => 'required_if:pumpSelector,1',
            'model'             => 'required_if:pumpSelector,1',
            // 'typeBinary'        => 'required_if:pumpSelector,0',
            // 'type'              => 'required_if:pumpSelector,0',
            // 'frequency'         => 'required_if:pumpSelector,0',
            // 'voltage'           => 'required_if:pumpSelector,0',
        ];
    }

    /**
     * messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'pumpSelector.required'         => __('project::pump.pumpSelector.required'),
            'q.required'                    => __('project::pump.q.required'),
            'h.required'                    => __('project::pump.h.required'),
        ];
    }
}
