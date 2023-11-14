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
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PumpStationCalculationRequest extends Request
{

    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'slug' => Str::slug($this->slug),
    //     ]);
    // }

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'q_all'             => 'required|numeric|between:0,1000',
            'h'                 => 'required|numeric|between:0,1000',
            'oid'               => 'required|exists:\Organizations\Model\Organizations,id',
            'manufacture'       => 'required',
            'series'            => 'required',
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
