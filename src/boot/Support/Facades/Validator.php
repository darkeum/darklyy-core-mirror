<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Schema facade class
*/

namespace Boot\Support\Facades;

use \Boot\System\Validation\Validator as SystemValidator;

class Validator
{
    public static function validate(array $rules, array $messages = [], array $customInput = [])
    {
        $validator = new SystemValidator;
        return $validator->validate($rules, $messages, $customInput);       
    }

    
  
}
