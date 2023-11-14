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
use GuzzleHttp;
use DOMDocument;
use Price\Parser\AbstractParser;

class Krepiz extends AbstractParser
{

    protected $search_field = "sku2";

    public function parse()
    {
        $client = static::client();

        ################################################# ПАРСЕР через DOMDocument
        #1 - Получаем ID
        $data = $client->request("GET", $this->url);
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<meta charset="utf-8">' . $data->getBody());
        $selector = new DOMXPath($dom);

        #1.1 - Если на странице есть кнопка то искать через ajax в каталоге
        $id = $selector->query("//@data-id");
        //dd($id->item(0)->nodeValue);
        if ($id->count() !== 0) {

            $data = $client->request(
                'POST',
                'https://www.krepiz.ru/catalogs/ajax.php',
                [
                    GuzzleHttp\RequestOptions::FORM_PARAMS => [
                        'elementid' => $id,
                    ],
                ]
            );
            @$dom->loadHTML('<meta charset="utf-8">' . $data->getBody());
            $selector = new DOMXPath($dom);
        }

        #2 - Получаем цену
        $id = $this->catalog->{$this->search_field};
        $nodes = $selector->query("//tr[td[contains(.,'" . $id . "')]]/*[@itemprop='highPrice']");
        $price = $nodes->item(0)->nodeValue;

        //dd($price);
        #################################################

        return [
            "price" => $price ?? 0,
        ];
    }
}
