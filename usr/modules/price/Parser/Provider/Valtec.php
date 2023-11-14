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

class Valtec extends AbstractParser
{
    protected $search_field = "sku";

    public function parse()
    {
        return $this->easy_parse('|<td>' . $this->catalog->{$this->search_field} . '.*<td class="rub">(.*)</td>.*</tr>|Uis');
    }
}
