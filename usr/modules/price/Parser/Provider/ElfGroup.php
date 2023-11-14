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

class ElfGroup extends AbstractParser
{
    protected $personal_area = true;

    public function parse()
    {
        //получение id товара
        $client = static::client();
        $data = $client->request("GET", $this->url);
        preg_match_all('|<div data-element="(.*)" class=".*">|Uis', $data->getBody(), $matches);
        if (isset($matches[1][0])) {
            $id = $this->clear_price($matches[1][0]);
        } else {
            throw new \Exception(AbstractParser::get_text_catalog_error("ID не обнаружен", $this->catalog));
        }

        //зайти для получения токена
        $itn = $this->get_login();
        $password = $this->get_password();
        $client_auth = static::client();
        $data = $client_auth->request("POST", "https://tula.elfgroup.ru/ajax/sign-in.php", [
            "form_params" => [
                "itn" => $itn,
                "password" => $password,
                "form_name" => "sign_in",
            ],
        ]);

        $url = "https://tula.elfgroup.ru/ajax/catalog/element.php";
        $data2 = $client->request("POST", $url, [
            "form_params" => [
                "PRODUCT_ID" => $id,
            ],
        ]);
        $pattern = '|<span class="catalog-detail-price-value".*>(.*)</span>|Uis';
        preg_match_all($pattern, $data2->getBody(), $matches);
        $price = $matches[1][0] ? $this->clear_price($matches[1][0]) : 0;
        $data3 = $client_auth->request("POST", $url, [
            "form_params" => [
                "PRODUCT_ID" => $id,
            ],
        ]);
        preg_match_all($pattern, $data3->getBody(), $matches);
        $purchase = $matches[1][0] ? $this->clear_price($matches[1][0]) : 0;
        $new_data = [
            "price" => $price,
            "purchase" => $purchase,
        ];
        return $new_data;
    }
}
