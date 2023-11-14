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

use Boot\Support\Currency;
use Price\Parser\AbstractParser;

class Siemens extends AbstractParser
{

    protected $personal_area = false;

    public function parse()
    {
        $new_data = ["price" => null];
        $client = static::client();
        //получение ссылки товара
        $data = $client->request("GET", $this->url);
        preg_match_all('|<input id="pDeepLink" type="hidden" value="(.*)"/>|Uis', $data->getBody(), $matches);
        if (isset($matches[1][0])) {
            $pDeepLink = $matches[1][0];
        } else {
            throw new \Exception(AbstractParser::get_text_catalog_error("pDeepLink не обнаружен, обратитесь к администратору", $this->catalog));
        }

        $data2 = $client->request("GET", "https://hit.sbt.siemens.com/RWD/{$pDeepLink}");
        $d = json_decode($data2->getBody(), true);

        foreach ($d as $item) {
            if (isset($item["id"]) && $item["id"] == "txtPrice") {
                $price = $item["desc"];
                $price = $this->clear_price($price);
                $price *= Currency::get_exchange()->get(978);
                $price = round($price, 2);
                $new_data["price"] = $price;
                return $new_data;
            }
        }
        return $new_data;
    }
}
