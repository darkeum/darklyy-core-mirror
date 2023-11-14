<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Decrees\UI\WEB\Requests;

use Boot\Abstracts\Requests\Request;

class CreateDecreeRequest extends Request
{
    // protected array $access = [
    //     'permission' => '',
    //     'roles'      => 'admin',
    // ];

    protected array $decode = [

    ];

    protected array $urlParameters = [

    ];

    public function rules(): array
    {
        return [
            'started_at'    => 'required',
            // 'password' => 'min:100|max:200',
            // 'name'     => 'min:300|max:400',
        ];
    }

    // public function authorize(): bool
    // {
    //     return $this->check([
    //         'hasAccess',
    //     ]);
    // }
}