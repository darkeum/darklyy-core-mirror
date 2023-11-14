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

use Price\UI\CLI\GenerateTypes\PriceGenerateType1;

class PriceGenerateType2 extends PriceGenerateType1{

    public function c_price_supplier(){
        parent::c_price_supplier();
        $this->c_price_supplier_correct();
    }

    public function c_price(){
        $price = round($this->price_supplier * (1 + $this->price_adjustments_price / 100), 2);
        $this->price = $price > $this->min_price ? $price : $this->min_price;
        // $this->price = round($this->price_supplier, 2);
    }

    public function c_price_site(){
        $price = round($this->price * ( $this->price_adjustments / 100 + 1 ), 2);
        $this->price_site = $price > $this->min_price ? ( $price <= $this->price ? $price : $this->price ) : $this->min_price;
    }

    public function с_discount_installers_mrc(){
        if( $this->price != 0){
            $this->discount_installers_mrc = round(100 - $this->mrc / $this->price * 100 , 2);
        } else {
            $this->discount_installers_mrc = 0;
        }
    
    }

    public function c_mrc(){
        $price = round($this->purchase/(100 - $this->optimal_margin) * 100, 2);
        // $this->mrc = $this->price > $price ? $price : $this->price;
        $this->mrc = $price > $this->min_price ? ( $price < $this->price_site ? $price : $this->price_site ) : $this->min_price;
    }

    public function calculate_view_data(){
        return $this->calculate_view_data_source();
    }
}