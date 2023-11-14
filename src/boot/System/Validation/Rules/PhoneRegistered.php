<?php

namespace Boot\System\Validation\Rules;

use \Boot\System\Validation\Rule;
use \Boot\Models\Users;

class PhoneRegistered extends Rule
{

    /** @var string */
    protected $message = "Телефон уже зарегистрирован";

    public function check($value): bool
    {
        if(Users::where('phone', $value)->count() <= 0) {
            return true;
        }       
        return false;
    }
}
