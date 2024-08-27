<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Validation;

use Illuminate\Validation\Validator as LaravelValidator;
use Boot\Contracts\Validation\Validator as ValidatorContract;

class Validator extends LaravelValidator implements ValidatorContract
{
    protected $modified_data;

    public function getModifiedData()
    {
        return $this->modified_data;
    }

    public function getModifiedField(string $key)
    {
        return isset($this->modified_data[$key]) ? $this->modified_data[$key] : (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    public function setModifiedField($key, $value)
    {
        $this->modified_data[$key] = $value;
    }

    public function setModifiedData($modified_data)
    {
        $this->modified_data = $modified_data;
    }

}
