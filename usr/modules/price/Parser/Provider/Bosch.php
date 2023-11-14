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

class Bosch extends AbstractParser
{

    protected $personal_area = true;

    public function parse()
    {
        $client = static::client();
        $data = $client->request("GET", "https://shop.bosch-climate.ru/bosch/login/?site=bosch");
        preg_match_all('|<input type="hidden" name="CSRFToken" value="(.*)" />|Uis', $data->getBody(), $matches);
        if (isset($matches[1][0])) {
            $token = $matches[1][0];
        } else {
            throw new \Exception(AbstractParser::get_text_catalog_error("Токен не обнаружен", $this->catalog));
        }
        $login = $this->get_login();
        $password = $this->get_password();
        $data = $client->request("POST", "https://shop.bosch-climate.ru/bosch/j_spring_security_check", [
            "form_params" => [
                "j_username" =>  $login,
                "j_password" => $password,
                "CSRFToken" => $token,
            ]
        ]);

        $data = $client->request("GET", $this->url);

        preg_match_all('|Итого с НДС</td>.*<td>(.*)</td>|Uis', $data->getBody(), $matches);
        $purchase = null;
        if (isset($matches[1][0])) {
            $purchase = $matches[1][0];
            $purchase = str_replace("Итого с НДС", "", $purchase);
            $purchase = str_replace(",", "", $purchase);
            $flag = strripos($purchase, "€") ? true : false;
            $purchase = $this->clear_price($purchase);
            if ($flag) $purchase *= Currency::get_exchange()->get(978);
            $purchase = round($purchase, 2);
        }
        preg_match_all('|Итого с НДС</td>.*<td>(.*)</td>|Uis', $data->getBody(), $matches);
        $price = null;
        if (isset($matches[1][0])) {
            $price = $matches[1][0];
            $price = str_replace("Цена без НДС", "", $price);
            $price = str_replace(",", "", $price);
            $flag = strripos($price, "€") ? true : false;
            $price = $this->clear_price($price);
            $price *= 1.2;
            if ($flag) $price *= Currency::get_exchange()->get(978);
            $price = round($price, 2);
        }
        return [
            "purchase" => $purchase,
            "price" => $price,
        ];
    }
}
