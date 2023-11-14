<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (19.10.2023 23:02)
*/

namespace EconomicNew\UI\WEB\Requests;

use Boot\System\Core\Abstracts\Requests\Request;

class EconomicActRequest extends Request
{
    /**
     * Правила валидации в модуле
     * @return array
     */
    public function rules(): array {
        return [
            'org' => 'required',
            'oid' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
        ];
    }

    public function messages(): array {
       return [
           'org.required' => 'Организация не указана',
           'oid.required' => 'Контрагент не указан',
           'date_start' => 'Дата начала не указана',
           'date_end' => 'Дата конца не указана',
       ];
    }

}