<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\UI\WEB\Requests;

use Boot\Abstracts\Requests\Request;

class SupportRequest extends Request
{
    /**
     * Правила фильтрации
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'comment' => 'required',
            'type' => 'required',
        ];
    }

    /**
     * Сообщения об ошибках
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'comment.required'         => __('support::errors.comment.required'),
            'type.required'            => __('support::errors.type.required')
        ];
    }
}
