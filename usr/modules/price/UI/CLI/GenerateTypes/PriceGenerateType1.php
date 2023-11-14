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

use Price\UI\CLI\PriceGenerateType;

class PriceGenerateType1 extends PriceGenerateType
{
    public function c_price()
    {
        $this->price = round($this->price_supplier, 2);
    }

    public function с_discount_installers_mrc()
    {
    }

    public function c_purchase()
    {
        $this->purchase = round($this->price_supplier * (1 - $this->our_supplier_discount / 100), 2);
    }

    public function c_mrc()
    {
        // $price = round($this->price_supplier * ( 1 - $this->discount_installers_mrc / 100 ), 2);
        // $this->mrc = $price >= $this->min_price ? $price : $this->min_price;
        $price = round($this->price * (1 - $this->discount_installers_mrc / 100), 2);
        $this->mrc = $price > $this->min_price ? ($price < $this->price_site ? $price : $this->price_site) : $this->min_price;
    }

    public function c_max_allowable_discount()
    {
        if ($this->price == 0) {
            dump($this);
        }
        if ($this->price != 0) {
            $this->max_allowable_discount = round(100 - $this->min_price / $this->price * 100, 2);
        } else {
            $this->max_allowable_discount = 0;
        }
    }

    public function calculate_view_data()
    {
        $data = parent::calculate_view_data();
        foreach ($data as $key => $item) {
            if (isset($item["field"]) && $item["field"] == "optimal_margin") {
                unset($data[$key]);
            }
        }
        $data = array_splice($data, 0);
        return $data;
    }
}
