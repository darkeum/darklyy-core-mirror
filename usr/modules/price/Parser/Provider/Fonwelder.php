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

class Fonwelder extends AbstractParser{

    public function parse(){
        $client = static::client();
        $data = $client->request("GET", $this->url);
        preg_match_all('|<span class="old-price">(.*)</span>|Uis', $data->getBody(), $matches);
        $price = 0;
        if(!empty($matches[1][0])){
            $price = $this->clear_price($matches[1][0]);
        } else {
            preg_match_all('|<span class="price">(.*)</span>|Uis', $data->getBody(), $matches);
            if(!empty($matches[1][0])){
                $price = $this->clear_price($matches[1][0]);
            }
        }
        return [
            "price" => $price,
        ];
    }
}
