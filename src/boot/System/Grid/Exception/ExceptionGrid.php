<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Exception;

use Exception;

class ExceptionGrid extends Exception
{
    protected $data;
    protected $exception_type = 'common';
    protected $output_type = 'json';

    public function __construct($message, $data = null, $code = 0)
    {
        $this->data = $data;
        return parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getExceptionType()
    {
        return $this->exception_type;
    }

    public function getOutputType()
    {
        return $this->output_type;
    }
}