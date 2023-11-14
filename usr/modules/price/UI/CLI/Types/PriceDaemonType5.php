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

class PriceDaemonType5 extends PriceDaemonType4
{
    public function parse_pd()
    {
        if ($this->supplier_price && $this->competing_price) {
            $this->pd = [
                "supplier_price" => [
                    "price" => $this->supplier_price["price"],
                    "purchase" => isset($this->supplier_price["purchase"]) ? $this->supplier_price["purchase"] : 0,
                    "discount" => $this->cpr->our_supplier_discount,
                ],
                "competing_price" => [
                    "items" => $this->competing_price,
                ]
            ];
        }
    }
}
