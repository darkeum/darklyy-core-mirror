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

class PriceGenerateType4 extends PriceGenerateType
{

    public function init()
    {
        parent::init();
        $this->price_supplier = empty($this->pd["supplier_price"]["price"]) ? 0 : $this->pd["supplier_price"]["price"];
    }

    public function c_price_supplier()
    {
        parent::c_price_supplier();
        $this->c_price_supplier_correct();
    }

    public function price_route()
    {
        $this->c_price_site();
        $this->c_price();
    }

    public function c_purchase()
    {
        $this->purchase = round($this->price_supplier * (1 - $this->our_supplier_discount / 100), 2);
    }

    public function c_min_price()
    {
        $this->min_price = round($this->min_margin > 0 ? ($this->purchase / (100 - $this->min_margin) * 100) : $this->purchase, 2);
    }

    public function c_price()
    {
        $this->price = round($this->price_supplier < $this->price_site ? $this->price_site : $this->price_supplier, 2);
    }

    static public function get_min_item($items, $value_func)
    {
        $min_price = null;
        $key = false;
        foreach ($items as $item_key => $item) {
            $value = $value_func($item);
            if ($value < $min_price || is_null($min_price)) {
                $min_price = $value;
                $key = $item_key;
            }
        }
        return $items[$key];
    }

    public function c_mrc()
    {
        $discount = function ($item) {
            return $item["price"] * (1 - ($item["discount"] / 100));
        };
        $min_item = static::get_min_item($this->pd["competing_price"]["items"], $discount);
        $price = round($discount($min_item) * (1 +  $this->price_adjustments / 100), 2);
        $this->mrc = $price > $this->min_price ? $price : $this->min_price;
    }

    public function c_max_allowable_discount()
    {
        $this->max_allowable_discount = round(100 - $this->min_price / $this->price * 100, 2);
    }

    public function с_discount_installers_mrc()
    {
        $this->discount_installers_mrc = round(100 - $this->mrc / $this->price * 100, 2);
    }

    public function c_price_site()
    {
        $min_item = static::get_min_item($this->pd["competing_price"]["items"], function ($item) {
            return $item["price"];
        });
        $min_price = $min_item["price"];
        $price = round($min_price * ($this->price_adjustments / 100 + 1), 2);
        $this->price_site = $price >= $this->min_price ? $price : $this->min_price;
    }

    public function calculate_view_data()
    {
        $data = parent::calculate_view_data();
        $items = $this->pd["competing_price"]["items"];
        foreach ($data as $key => $item) {
            if (isset($item["field"]) && $item["field"] == "optimal_margin") {
                unset($data[$key]);
            }
        }
        $data = array_splice($data, 0);
        foreach ($items as $item) {
            $ii = (object) $item;
            array_unshift($data, ["rus" => "Прайс конкурента {$ii->host}", "value" => $ii->price]);
            array_unshift($data, ["rus" => "Скидка конкурента {$ii->host}", "value" => $ii->discount]);
        }
        return $data;
    }
}
