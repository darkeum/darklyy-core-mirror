<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Feedback\UI\API\Requests;

use Boot\Abstracts\Requests\Request;

class SendRequest extends Request
{
    public function rules(): array
    {
        return [
            'ids' => 'required',
            // 'oid' => 'required',
            // 'object' => 'required',
        ];
    }
}
