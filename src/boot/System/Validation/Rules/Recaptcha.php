<?php

namespace Boot\System\Validation\Rules;

use Boot\System\Validation\Rule;

class Recaptcha extends Rule
{

    /** @var string */
    protected $message = "Recaptcha is not valid";

    public function check($value): bool
    {
        // return true;

        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

        $recaptcha_secret = '6LfddtUZAAAAAOJmTgbOOIec5WaFIOnIrOqBFk7I';
        // $recaptcha_secret = '6LcA9sghAAAAAO32aLTpzrjFIju0bLtt8qLGenbA';
        $recaptcha_response = $value;

        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha);

        if($recaptcha->success == true && $recaptcha->score >= 0.5 && $recaptcha->action == 'contact') {
            return true;
        }
        return false;
    }
}
