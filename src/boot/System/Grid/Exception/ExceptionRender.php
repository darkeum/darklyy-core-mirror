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

class ExceptionRender extends ExceptionGrid
{
    protected $exception_type = 'render';
    protected $output_type = 'trigger_error';

    #
    public function __toString()
    {
        return $this->getMessage();
    }
}
