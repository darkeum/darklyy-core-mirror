<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Parser\Provider;

use Price\Parser\AbstractParser;

class Insat extends AbstractParser
{

    public function parse()
    {
        $data = $this->easy_parse('|<span class="summa">(.*)</span>|Uis');
        $data["price"] = round($data["price"], 2);
        return $data;
    }
}
