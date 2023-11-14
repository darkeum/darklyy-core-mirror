<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description In rule for validation
*/

namespace Boot\System\Validation\Rules;

use Boot\System\Validation\Helper;
use Boot\System\Validation\Rule;

class In extends Rule
{

    protected $message = "The :attribute only allows :allowed_values";


    protected $strict = false;

    /**
     * Given $params and assign the $this->params
     *
     * @param array $params
     * @return self
     */
    public function fillParameters(array $params): Rule
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['allowed_values'] = $params;
        return $this;
    }

    /**
     * Set strict value
     *
     * @param bool $strict
     * @return void
     */
    public function strict(bool $strict = true)
    {
        $this->strict = $strict;
    }

    /**
     * Check $value is existed
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters(['allowed_values']);

        $allowedValues = $this->parameter('allowed_values');

        $or = $this->validation ? $this->validation->getTranslation('or') : 'or';
        $allowedValuesText = Helper::join(Helper::wraps($allowedValues, "'"), ', ', ", {$or} ");
        $this->setParameterText('allowed_values', $allowedValuesText);

        return in_array($value, $allowedValues, $this->strict);
    }
}
