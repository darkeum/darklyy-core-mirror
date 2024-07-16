<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Data;

class Value extends Data
{
    #
    public function __construct(array $data, $first = null)
    {
        if(!is_null($first))
        {
            $data = array('' => $first) + $data;
        }

        parent::__construct($data);
    }

    #
    public function __toString()
    {
        $base = array();

        foreach($this->data as $k => $v)
        {
            $base[] = $k . ':' . addslashes($v);
        }

        return '"' . implode(';', $base) . '"';
    }

}
