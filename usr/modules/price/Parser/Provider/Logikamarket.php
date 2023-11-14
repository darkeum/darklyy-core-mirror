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

class Logikamarket extends AbstractParser
{
    static $charset = "windows-1251";
    protected $search_field = "sku2";

    public function parse()
    {
        return $this->easy_parse('|<td class="sku">' . $this->catalog->{$this->search_field} . '.*<td class="mprice">(.*) <i|Uis');
    }
}
