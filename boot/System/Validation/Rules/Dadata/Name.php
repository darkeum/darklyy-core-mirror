<?php

namespace Boot\System\Validation\Rules\Dadata;

use \Boot\System\Validation\Rule;
use \Dadata\DadataClient;
use \Boot\System\Validation\Rules\Interfaces\ModifyValue;


class Name extends Rule implements ModifyValue
{

    /** @var string */
    protected $message = "Пожалуйста укажите реальное имя";
    protected $response = null;

    public function check($value): bool
    {       
        if ($this->response['qc'] == '0') {
            return true;
        }        
        return false;
    }

    public function modifyValue($value)
    {
        $dadata = new DadataClient('ddeafb9ff43e79578243d8f031f6e9d8b047ca0e', 'e03a8f8ceaa114f8d8e0e09af92a0f3b069f1d40');
        $this->response = $dadata->clean("name", $value);        
        return  $this->response;
    }
}
