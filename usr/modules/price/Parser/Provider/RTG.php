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

class RTG extends AbstractParser
{

    // public $cookies = "geobase=a%3A7%3A%7Bs%3A7%3A%22inetnum%22%3Bs%3A28%3A%2287.249.224.0+-+87.250.31.255%22%3Bs%3A7%3A%22country%22%3Bs%3A2%3A%22RU%22%3Bs%3A4%3A%22city%22%3Bs%3A12%3A%22%D0%98%D0%B6%D0%B5%D0%B2%D1%81%D0%BA%22%3Bs%3A6%3A%22region%22%3Bs%3A37%3A%22%D0%A0%D0%B5%D1%81%D0%BF%D1%83%D0%B1%D0%BB%D0%B8%D0%BA%D0%B0+%D0%A3%D0%B4%D0%BC%D1%83%D1%80%D1%82%D0%B8%D1%8F%22%3Bs%3A8%3A%22district%22%3Bs%3A56%3A%22%D0%9F%D1%80%D0%B8%D0%B2%D0%BE%D0%BB%D0%B6%D1%81%D0%BA%D0%B8%D0%B9+%D1%84%D0%B5%D0%B4%D0%B5%D1%80%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9+%D0%BE%D0%BA%D1%80%D1%83%D0%B3%22%3Bs%3A3%3A%22lat%22%3Bs%3A9%3A%2256.842609%22%3Bs%3A3%3A%22lng%22%3Bs%3A9%3A%2253.224449%22%3B%7D; BITRIX_SM_RTG_CITY2=%D0%98%D0%B6%D0%B5%D0%B2%D1%81%D0%BA; BITRIX_SM_SALE_UID=1687413; _ym_uid=1594793165335866509; _ym_d=1594793165; _ga=GA1.2.772424167.1594793165; _gid=GA1.2.1714361918.1594793165; _ym_isad=1; BITRIX_CONVERSION_CONTEXT_s1=%7B%22ID%22%3A2%2C%22EXPIRE%22%3A1594846740%2C%22UNIQUE%22%3A%5B%22conversion_visit_day%22%5D%7D; BX_USER_ID=30aead7b36f8efec5f43be9e8a1be926; _ym_visorc_14719411=w; PHPSESSID=c44a5fb432b6a29166b5dfd744f44f43";

    // public function fields(){
    //     return [
    //         [
    //             "field" => "price",
    //             "reg" => '|<div class="goods-card-price__value">(.*)</div>|Uis',
    //             "clear_after" => function($request){
    //                 $new_value = $request["value"];
    //                 $new_value = $this->clear_price($new_value);
    //                 return $new_value;
    //             },
    //         ],
    //         // [
    //         //     "field" => "name",
    //         //     "reg" => '|<h1 class="header-page" itemprop="name">(.*)</h1>|Uis',
    //         // ]
    //     ];
    // }

    public function parse()
    {
        return $this->easy_parse('|<div class="goods-card-price__value">(.*)</div>|Uis');
    }
}
