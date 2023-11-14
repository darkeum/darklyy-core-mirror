<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\Types;

use Price\UI\CLI\Types\PriceDaemonType2;

class PriceDaemonType3 extends PriceDaemonType2
{
    public function parse()
    {
        parent::parse();
        if (empty($this->pd["purchase"])) {
            throw new \Exception($this->get_text_error("поставщика в личном кабинете"));
        }
    }
}
