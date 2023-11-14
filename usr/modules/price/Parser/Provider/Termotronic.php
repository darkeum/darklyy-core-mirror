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

class Termotronic extends AbstractParser
{
    protected $search_field = "sku";

    public function parse()
    {
        $data = $this->easy_parse('|<dl data-art="' . $this->catalog->{$this->search_field} . '">(.*)</dl>|Uis');
        $data["price"] = round($data["price"] * 1.2, 2);
        return $data;
    }
}
