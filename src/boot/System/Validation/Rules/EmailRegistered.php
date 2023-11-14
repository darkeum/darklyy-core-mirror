<?php

namespace Boot\System\Validation\Rules;

use \Boot\System\Validation\Rule;
use \Boot\Models\Users;

class EmailRegistered extends Rule
{

    /** @var string */
    protected $message = "Email уже зарегистрирован";

    public function check($value): bool
    {
        if(Users::where('email', $value)->count() <= 0) {
            return true;
        }       
        return false;
    }
}
