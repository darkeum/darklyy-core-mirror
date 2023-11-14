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

class Teplovodomer extends AbstractParser
{

    public function get_pattern($class)
    {
        return '|<div class="' . $class . '">.*<span .*>(.*)<i.*.*</div>|Uis';
    }

    public function parse()
    {
        $client = static::client();
        $data = $client->request("GET", $this->url);
        preg_match_all($this->get_pattern("product-price-last"), $data->getBody(), $matches);
        $price = 0;
        if (!empty($matches[1][0])) {
            $price = $this->clear_price($matches[1][0]);
        } else {
            preg_match_all($this->get_pattern("product-price"), $data->getBody(), $matches);
            if (!empty($matches[1][0])) {
                $price = $this->clear_price($matches[1][0]);
            }
        }
        return [
            "price" => $price,
        ];
    }
}
