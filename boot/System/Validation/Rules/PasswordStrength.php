<?php

namespace Boot\System\Validation\Rules;

use Boot\System\Validation\Rule;
use ZxcvbnPhp\Zxcvbn;
use \Boot\Support\Str;

class PasswordStrength extends Rule
{
    protected $message = "Пароль не соответствует критериям надежности";

    public function fillParameters(array $params): Rule
    {
        // dd($params);
        $this->params['scope'] = array_shift($params);
        $this->userData($params);

        return $this;
    }
    
    public function userData($data): Rule
    {
        if (is_string($data)) {
            $data = explode('|', $data);
        }

        $this->params['user_data'] = $data;

        return $this;
    }

    public function check($value): bool
    { 
        $zxcvbn = new Zxcvbn();
        $scope = $this->parameter('scope');
        $userDataWithTranslite = [];
        if(!empty($this->parameter('user_data'))){
            $userData = $this->parameter('user_data');
            foreach ($userData as $v) {
                $userDataWithTranslite[] = $v;
                $userDataWithTranslite[] = Str::translite($v);               
            }
        }  
        if(empty($scope)){
            $scope = 3;
        }
        $weak = $zxcvbn->passwordStrength($value, $userDataWithTranslite);
        if($weak['score'] >= $scope) {
            return true;
        }
        return false;       
    }
}
