<?php

namespace Boot\System\Exceptions\Ignition\Http\Requests;

use Boot\System\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConfigRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'theme' => ['required',  Rule::in(['light', 'dark', 'auto'])],
            'editor' => ['required'],
            'hide_solutions' => ['required', 'boolean'],
        ];
    }
}
