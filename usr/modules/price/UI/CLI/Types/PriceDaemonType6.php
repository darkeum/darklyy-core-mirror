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

use Price\UI\CLI\Types\PriceDaemonType4;

class PriceDaemonType6 extends PriceDaemonType4
{
    public function parse_supplier_price()
    {
        $this->price_auto = 0;
        $data = $this->get_local_price();
        $price = empty($data->price) ? false : $data->price;
        $this->price_type = $data->price_type;
        if (empty($price)) {
            $this->error_us();
        } else {
            $this->supplier_price = ["price" => $price];
        }
    }
}
