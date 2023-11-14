<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Exceptions;

class OptionsException extends \Exception
{
    private $_options;

    public function __construct(
        $message,
        $code = 0,
        \Exception $previous = null,
        $options = []
    ) {
        parent::__construct($message, $code, $previous);

        $this->_options = $options;
    }

    public function get_options()
    {
        return $this->_options;
    }
}
