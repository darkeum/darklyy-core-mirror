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

class UdmurtSnab extends AbstractParser
{

    protected $personal_area = false;

    static $charset = "windows-1251";

    protected $search_field = "sku_UdmurtSnab";

    public function parse()
    {
        return $this->easy_parse('|<td>' . $this->catalog->{$this->search_field} . '.*<td class="price">(.*)</td>.*</tr>|Uis');
    }
}
