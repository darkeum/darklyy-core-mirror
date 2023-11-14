<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Parser\Provider;

use Price\Parser\AbstractParser;

class Tartariya extends AbstractParser
{

    protected $search_field = "sku2";

    // public function fields(){
    //     return [
    //         [
    //             "field" => "price",
    //             "reg" => function(){
    //                 $sku = str_replace("*", "\*", $this->catalog->{$this->search_field});
    //                 return '|'.$sku.'</span>(.*) руб|Uis';
    //             },
    //             "value_selection" => 1,
    //             "clear_after" => function($request){
    //                 $new_value = $request["value"];
    //                 $new_value = $this->clear_price($new_value);
    //                 return $new_value;
    //             },
    //         ],
    //     ];
    // }

    public function parse()
    {
        $sku = str_replace("*", "\*", $this->catalog->{$this->search_field});
        return $this->easy_parse('|' . $sku . '</span>(.*) руб|Uis');
    }
}
