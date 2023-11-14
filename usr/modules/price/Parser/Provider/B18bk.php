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

class B18bk extends AbstractParser
{
    public function parse()
    {
        return $this->easy_parse('|<span id="our_price_display" .*>(.*)</span>|Uis');
    }
}
