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

use Price\UI\CLI\PriceDaemonType;

class PriceDaemonType1 extends PriceDaemonType
{
    public function parse()
    {
        $this->price_auto = 0;
        // $price = $this->get_local_price();
        $data = $this->get_local_price();
        $price = empty($data->price) ? false : $data->price;
        if ($price) {
            $this->pd = [
                "price" => $price,
                "price_type" => $data->price_type,
            ];
        } else {
            $this->error_us();
        }
    }
}
