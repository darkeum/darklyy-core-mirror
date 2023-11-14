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

use DOMXPath;
use DOMElement;
use DOMDocument;

use PHPHtmlParser\Dom;

use Price\Parser\AbstractParser;
use Price\Exceptions\ExceptionPrice;

class SantekhKomplekt extends AbstractParser
{

    protected $personal_area = true;

    public function parse()
    {
        // Получение
        $client = static::client();
        $new_data = [];

        $url = $this->url;

        // Получаем price
        $dom = new Dom;
        $dom->loadFromUrl($url);

        // <meta itemprop="price" content="618700">
        $price = $dom->find('meta[itemprop="price"]');
        if (!isset( $price[0])) {
            throw new \Exception(AbstractParser::get_text_catalog_error("Не найдена цена т.к. ID не обнаружен", $this->catalog));
        }
        $price = $price[0]->getAttribute('content');
        // $new_data['price'] = floatval($price) ?? 0;
        $new_data['price'] = (double)$price ?? 0;
        // dd($new_data['price']);

        // Получаем ID для purchase
        $id = $dom->find('span.ss-js-price[data-item=*]');
        $id = $id[0]->getAttribute('data-item');
        // dd($id);

        if (!isset($id)) {
            throw new \Exception(AbstractParser::get_text_catalog_error("Не найдена цена т.к. ID не обнаружен", $this->catalog));
        }

        // Получаем purchase
        // Логинемся
        $client->request("POST", "https://www.santech.ru/welcome/login/?auth", [
            "form_params" => [
                "email" => $this->get_login(),
                "pass" => $this->get_password(),
                "ajax" => 1,
            ],
        ]);

        $data = $client->request("POST", "https://www.santech.ru/catalog/getPrices/", [
            "form_params" => [
                "variants[]" => $id,
                "json",
            ],
            // "cookies" => static::CookieJar("https://www.santech.ru/catalog/getPrices/", "_ga=GA1.2.1425421457.1604578192; __utmz=1.1604578192.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ym_d=1604578192; _ym_uid=1604578192285030032; filial_key=msk; __utma=1.1425421457.1604578192.1617775974.1618569284.23; __utmc=1; __utmt=1; _gid=GA1.2.448875163.1618569284; _ym_visorc=w; _ym_isad=1; primary=9b6030d865fe00090cf0d5e5a17cc891; order=2597986; user_id=12029; pass_hash=80ebead38363a9b000b97e754a7994d6; hash=3e23849e62ce62e75a2172e8bfa764ee; auth=b6963580aa31495c848512fc5f6b8d55; _gat=1; _gat_UA-28461799-1=1; __utmb=1.6.10.1618569284")
        ]);

        // Парсим purchase
        $dom = new Dom;
        $dom->loadStr($data->getBody());
        // dd($dom->outerHtml);
        $purchase = json_decode($dom->outerHtml);
        $purchase = collect($purchase->data->prices);
        // dd($purchase);

        $purchase = $purchase->first();
        $price_variant = $purchase->price_variant;
        // $user_price = $price->user_price;
        // dd($price_variant);
        $new_data['purchase'] = (double)$price_variant ?? 0;

        //получение price
        // $new_data = [
        //     "price" => $matches[1][0] ? $this->clear_price($matches[1][0]) : 0,
        // ];

        // $new_data["purchase"] = isset($data2["data"]["prices"][$id]["price_variant"]) ? $data2["data"]["prices"][$id]["price_variant"] : 0;

        if ($new_data["purchase"] == $new_data["price"]) {
            if (empty($new_data["purchase"])) {
                throw new ExceptionPrice(AbstractParser::get_text_catalog_error("Товар не обнаружен", $this->catalog));
            } else {
                throw new \Exception(AbstractParser::get_text_catalog_error("Цена закупа совпадает с прайсом", $this->catalog));
            }
        }

        if ($new_data["purchase"] > $new_data["price"]) {
            throw new ExceptionPrice(AbstractParser::get_text_catalog_error("Закуп больше прайса", $this->catalog));
        }

        // dd($new_data);

        return $new_data;
    }
}
