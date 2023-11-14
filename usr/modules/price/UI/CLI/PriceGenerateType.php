<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI;

abstract class PriceGenerateType
{
    public $price_supplier;             //Прайс Поставщика
    public $purchase;                   //Закуп
    public $price_adjustments;          //% корректировки прайса, МРЦ
    public $price_adjustments_price;    //% корректировки прайса Атри-Энерго
    public $min_margin;                 //Мин. Маржа
    public $optimal_margin;             //Маржа оптимальная
    public $mrc_margin;                 //Маржа по МРЦ
    public $min_price;                  //Мин цена
    public $price;                      //Прайс Атри-Энерго
    public $mrc;                        //МРЦ (монтажник)
    public $max_allowable_discount;     //Макс допустимая скидка
    public $discount_installers_mrc;    //Скидка для монтажников по МРЦ
    public $discount_customers;         //Скидка для заказчиков
    public $discount_designers;         //Скидка проектировщиков
    public $our_supplier_discount;      //Наша скидка у Поставщика
    public $price_site;                 //Прайс для сайта
    public $correction_factor_price;    //Коэффициент корректировки Прайса поставщика и закупа

    protected $correction_factor = 0.3; //Поправочный коэффициент
    static $group_count = 10;

    public $cpr;                        //Данные таблицы JMY_catalog_price_rival
    public $pd;                         //Данные комунизма

    abstract public function c_price();

    abstract public function c_mrc();

    abstract public function c_max_allowable_discount();

    abstract public function с_discount_installers_mrc();

    public function init()
    {
        $this->our_supplier_discount   = empty($this->cpr->our_supplier_discount) ? 0 : $this->cpr->our_supplier_discount;
        $this->price_supplier          = empty($this->pd["price"]) ? 0 : $this->pd["price"];
        $this->price_adjustments       = empty($this->cpr->price_adjustments) ? 0 : $this->cpr->price_adjustments;
        $this->price_adjustments_price = empty($this->cpr->price_adjustments_price) ? 0 : $this->cpr->price_adjustments_price;
        $this->min_margin              = empty($this->cpr->min_margin) ? 0 : $this->cpr->min_margin;
        $this->optimal_margin          = empty($this->cpr->optimal_margin) ? 0 : $this->cpr->optimal_margin;
        $this->discount_installers_mrc = empty($this->cpr->discount_installers_mrc) ? 0 : $this->cpr->discount_installers_mrc;
        $this->correction_factor_price = empty($this->cpr->correction_factor_price) ? 0 : $this->cpr->correction_factor_price;
    }

    static public function get_discount_name_list()
    {
        $result = [1 => "discount_installers_mrc", "discount_customers", "discount_designers"];
        for ($i = 1; $i <= self::$group_count; $i++) {
            $result[] = "group_" . $i;
        }
        return $result;
    }

    static public function get_discount_name_list_rus($type = null)
    {
        $list = [1 => "Скидка для монтажников по МРЦ", "Скидка для заказчиков", "Скидка проектировщиков"];
        for ($i = 1; $i <= self::$group_count; $i++) {
            $list[] = "Группа " . $i;
        }
        return is_null($type) ? $list : $list[$type];
    }

    public function calculate()
    {
        $this->c_price_supplier();
        $this->c_purchase();
        $this->c_min_price();
        $this->price_route();
        $this->c_mrc();
        $this->c_max_allowable_discount();
        $this->с_discount_installers_mrc();
        $this->c_discount_customers();
        $this->c_discount_designers();
        $this->c_group();

        $this->c_mrc_margin();
    }

    public function price_route()
    {
        $this->c_price();
        $this->c_price_site();
    }

    public function c_price_supplier()
    {
        $this->price_supplier = round($this->price_supplier, 2);
    }

    public function c_price_supplier_correct()
    {
        $this->price_supplier = empty($this->correction_factor_price) ? $this->price_supplier : round($this->price_supplier * $this->correction_factor_price, 2);
    }

    static function calculate_view_data_source_list()
    {
        $data = [
            [
                "rus" => "Наша скидка у Поставщика",
                "field" => "our_supplier_discount",
            ],
            [
                "rus" => "Прайс Поставщика",
                "field" => "price_supplier",
            ],
            [
                "rus" => "Закуп",
                "field" => "purchase",
            ],
            [
                "rus" => "% Скидка у поставщика",
                "field" => "supplier_discount",
            ],
            [
                "rus" => "Коэффициент корректировки Прайса поставщика и закупа",
                "field" => "correction_factor_price",
            ],
            [
                "rus" => "% корректирвки прайса, МРЦ",
                "field" => "price_adjustments",
            ],
            [
                "rus" => "% коректировки прайса, Атри-Энерго",
                "field" => "price_adjustments_price",
            ],
            [
                "rus" => "Мин. Маржа",
                "field" => "min_margin",
            ],
            [
                "rus" => "Маржа оптимальная",
                "field" => "optimal_margin",
            ],
            [
                "rus" => "Маржа по МРЦ",
                "field" => "mrc_margin",
            ],
            [
                "rus" => "Мин цена",
                "field" => "min_price",
            ],
            [
                "rus" => "Прайс для сайта",
                "field" => "price_site",
            ],
            [
                "rus" => "Прайс Атри-Энерго",
                "field" => "price",
            ],
            [
                "rus" => "МРЦ (монтажник)",
                "field" => "mrc",
            ],
            [
                "rus" => "Макс допустимая скидка",
                "field" => "max_allowable_discount",
            ],
        ];
        $keys = self::get_discount_name_list();
        $values = self::get_discount_name_list_rus();
        $discount = array_combine($keys, $values);
        foreach ($discount as $field => $rus) {
            $data[] = [
                "rus" => $rus,
                "field" => $field,
            ];
        }
        return $data;
    }

    public function calculate_view_data_source()
    {
        $data = static::calculate_view_data_source_list();
        foreach ($data as $key => $item) {
            $field = $item["field"];
            switch ($field) {
                default:
                    $data[$key]["value"] = isset($this->$field) ? $this->$field : null;
                    break;
                case "supplier_discount":
                    $data[$key]["value"] = round(100 - $this->purchase / $this->price_supplier * 100, 2);
                    break;
            }
        }
        return $data;
    }

    public function calculate_view_data()
    {
        return $this->calculate_view_data_source();
    }

    public function calculate_view()
    {
        $data = $this->calculate_view_data();
        foreach ($data as $item) {
            dump($this->calculate_view_template($item));
        }
    }

    public function c_mrc_margin()
    {
        if ($this->mrc != 0) {
            $this->mrc_margin = round(($this->mrc - $this->purchase) / $this->mrc * 100, 2);
        } else {
            $this->mrc_margin = 0;
        }
    }

    public function calculate_view_template($item)
    {
        return "{$item['rus']}: {$item['value']}";
    }

    public function c_purchase()
    {
    }

    public function c_purchase_correct()
    {
        $this->purchase = empty($this->correction_factor_price) ? $this->purchase : round($this->purchase * $this->correction_factor_price, 2);
    }

    public function c_min_price()
    {
        // $this->min_price = round( $this->purchase / (100 - $this->min_margin) * 100 , 2);
        $price = round($this->purchase / (100 - $this->min_margin) * 100, 2);
        $this->min_price = $price > $this->purchase ? $price : $this->purchase;
    }

    public function c_price_site()
    {
        $price = round($this->price_supplier * ($this->price_adjustments / 100 + 1), 2);
        $this->price_site = $price > $this->min_price ? ($price <= $this->price ? $price : $this->price) : $this->min_price;
    }

    public function c_discount_customers()
    {
        $this->discount_customers = round(abs($this->discount_installers_mrc) * $this->correction_factor, 1);
    }

    public function c_discount_designers()
    {
        $this->discount_designers = 0;
    }

    public function c_group()
    {
        for ($i = 1; $i <= static::$group_count; $i++) {
            $name = "group_" . $i;
            // ЕСЛИ((($Q3-ABS($R3))/10*U$1+ABS($R3))>$Q3;$Q3;($Q3-ABS($R3))/10*U$1+ABS($R3))
            $condition = ($this->max_allowable_discount - abs($this->discount_installers_mrc)) / static::$group_count * $i + abs($this->discount_installers_mrc);
            $value = $condition > $this->max_allowable_discount ? $this->max_allowable_discount : $condition;
            $this->$name = round($value, 2);
        }
    }

    public function get_price()
    {
        return $this->price;
    }

    public function get_purchase()
    {
        return $this->purchase;
    }

    public function get_price_site()
    {
        return $this->price_site;
    }
}
