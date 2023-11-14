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

use Price\UI\CLI\GenerateTypes\PriceGenerateType2;

class PriceGenerateType3 extends PriceGenerateType2{

    public function init(){
        parent::init();
        $this->purchase = empty($this->pd["purchase"]) ? 0 : $this->pd["purchase"];
    }

    public function c_purchase(){
        $this->c_purchase_correct();
    }

    public function calculate_view_data(){
        $data = $this->calculate_view_data_source();
        foreach($data as $key => $item){
            if(isset($item["field"]) && $item["field"] == "our_supplier_discount"){
                unset($data[ $key ]);
            }
        }
        $data = array_splice($data, 0);
        return $data;
    }
}