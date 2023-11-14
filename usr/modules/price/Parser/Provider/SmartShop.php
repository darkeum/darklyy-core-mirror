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

class SmartShop extends AbstractParser
{

    protected $personal_area = true;

    public function parse()
    {
        $client = static::client();
        $data = $client->request("GET", $this->url);
        $pattern = '|class="copy-select-val text-nowrap">(.*)</span>|Uis';

        preg_match_all($pattern, $data->getBody(), $price);
        //проверка на наличие
        if (str_contains($data->getBody(), '<div class="h4 font-weight-bold mb-0">Товара нет в наличии</div>')) {
            throw new \Exception(AbstractParser::get_text_catalog_error("Не найдена цена т.к. товара нет в наличии", $this->catalog));
        }

        preg_match('|<meta name="csrf-token" content="(.*)"|Uis', $data->getBody(), $login_csrf);

        // $this->url = "https://cabinet.smart-shop.pro/cgi-bin/display2.exe/commitlogon.mml";
        $data3 = $client->request("POST", "https://smart-shop.pro/login", [
            "form_params" => [
                "login" => $this->get_login(),
                "password" => $this->get_password(),
            ],
            'headers'     => ['X-CSRF-Token' => $login_csrf[1]],
            'allow_redirects' => true,
        ]);
        // dd( $data3->getBody()->getContents());
        $data3 = $client->request("GET", $this->url, ['headers'     => ['X-CSRF-Token' => $login_csrf[1]]]);
        preg_match_all('/<div class="h6">Smart-Shop.pro<\/div>.*class="copy-select-val text-nowrap">(.*)<\/span>.*<div class="h6">Выберите стороннего поставщика/Uis', $data3->getBody(), $purchase);
        // print_r( $data3->getBody()->getContents());
        // exit;
        // dd($purchase);
        if (!isset($purchase[1][0])) {
            preg_match_all($pattern, $data3->getBody(), $purchase);
        }
        // dd($purchase);

        $new_data = [
            "price" => $this->clear_price($price[1][0] ?? 0),
            "purchase" => $this->clear_price($purchase[1][1] ?? $purchase[1][0] ?? 0),
        ];
        return $new_data;
    }
}
