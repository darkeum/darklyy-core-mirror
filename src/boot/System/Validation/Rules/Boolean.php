<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Boolean rule for validation
*/

namespace Boot\System\Validation\Rules;

use Boot\System\Validation\Rule;

class Boolean extends Rule
{
        
    /**
     * Сообщение об ошибке
     *
     * @var string
     */
    protected $message = "The :attribute must be a boolean";

    /**
     * Проверка переменной на валидность
     *
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function check($value): bool
    {
        return \in_array($value, [\true, \false, "true", "false", 1, 0, "0", "1", "y", "n"], \true);
    }
}
