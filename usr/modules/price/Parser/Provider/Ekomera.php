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
use Price\Parser\AbstractParser;

class Ekomera extends AbstractParser
{

    public function parse()
    {

        $client = static::client();

        ################################################# ПАРСЕР через DOMDocument
        $data = $client->request("GET", $this->url, ['http_errors' => false]);
        //dd($data);

        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<meta charset="utf-8">' . $data->getBody());
        $selector = new DOMXPath($dom);
        $nodes = $selector->query("//span[@itemprop='price']");

        $price = $nodes->item(0)->nodeValue;
        $price = preg_replace('/[^0-9]/', '', trim($price)); // Полная чистка (только цифры)

        //dd($price);
        #################################################

        return [
            "price" => $price,
        ];
    }
}
