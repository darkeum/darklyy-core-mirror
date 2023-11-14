<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\GenerateTypes;

use Price\UI\CLI\GenerateTypes\PriceGenerateType4;

class PriceGenerateType5 extends PriceGenerateType4
{

    public function init()
    {
        parent::init();
        $this->purchase = empty($this->pd["supplier_price"]["purchase"]) ? 0 : $this->pd["supplier_price"]["purchase"];
    }

    public function c_purchase()
    {
    }

    public function calculate_view_data()
    {
        $data = parent::calculate_view_data();
        foreach ($data as $key => $item) {
            if (isset($item["field"]) && $item["field"] == "our_supplier_discount") {
                unset($data[$key]);
            }
        }
        $data = array_splice($data, 0);
        return $data;
    }
}
