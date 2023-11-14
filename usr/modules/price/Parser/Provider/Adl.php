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

class Adl extends AbstractParser
{
    protected $search_field = "sku";

    // Образцовый парсер просьба ни чего не удалять !!!

    public function parse()
    {
        $client = static::client();
        //$data = $client->request("GET", $this->url);

        ################################################# ПАРСЕР через DOMDocument
        $parts = parse_url($this->url);
        //dd($parts);
        $url = explode('?', $this->url)[0];
        //dd($url);

        // ?tablep=0 - Обязательный параметр для сайта ADL.ru
        //?set_filter=Y - Основа для парсера
        if (stripos($parts['query'] ?? false, 'filter')) {
            $params = $parts['query'] . '&tablep=0';
        } else {
            $params = 'tablep=0';
        }
        //dd($params);

        $data = $client->request("GET",  $url . '?' . $params, [
            "cookies" => static::CookieJar($url, "PHPSESSID=bAJmXa6MAWx2n33W2kROnHbN5jTW9Ihc; BITRIX_SM_GUEST_ID=5247932; _ga=GA1.2.1253590666.1595576122; _gid=GA1.2.989948641.1595576122; top100_id=t1.4504784.1199829135.1595576122057; _ym_uid=1595576122132091534; _ym_d=1595576122; BX_USER_ID=30aead7b36f8efec5f43be9e8a1be926; _ym_visorc_15504391=w; _ym_isad=1; BITRIX_SM_SALE_UID=245076; BX_CITY=465; BITRIX_SM_LAST_VISIT=24.07.2020+11%3A08%3A21; MIN_QUERY_LEN=1; last_visit=1595563474193::1595577874193")
        ]);

        ################################################# ПАРСЕР через DOMDocument
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<meta charset="utf-8">' . $data->getBody());
        $selector = new DOMXPath($dom);
        //dd($selector);

        $id = $this->catalog->{$this->search_field};
        //$table = $dom->getElementsByTagName('table'); # Получение таблицы

        ## ШПОРА
        //dd($this->catalog);
        //dd($this->catalog->{'unit'});
        ## ->query("//*[normalize-space(text())='IJ03A109045']"); // BR01B531977
        ## ->query("//tr/td[normalize-space(text())='IJ03A109045']/.."); // Получение родителя + поиск
        ## ->query('//span[@itemprop="price"]') или ->query('//td[@class="table__last"]')
        ## ->nodeValue; // textContent
        ## ->parentNode
        ## ->childElementCount
        ## ->lastElementChild
        ## ->item(0)

        ### УНИВЕРСАЛЬНЫЙ ЯКОРЬ ###
        //$nodes = $selector->query("//tr[td[contains(.,'IJ03A109045')] and td[contains(.,'S101005170N')]]"); // Получение родителя по 2 и более параметрам (Столбцы Артикул и Маркировка)

        //$nodes = $selector->query("//tr[td[contains(.,'". $id ."')]]");
        //dd($nodes->item(0)->lastElementChild->childElementCount);
        //dd($nodes->item(0)->nodeValue);

        $nodes = $selector->query("//tr[td[contains(.,'" . $id . "')]]/*/*[@itemprop='price'] | //tr[td[contains(.,'" . $id . "')]]/*[@class='table__last']");
        $price = $nodes->item(0)->nodeValue;
        $price = preg_replace('/[^0-9]/', '', trim($price)); // Полная чистка (только цифры)
       
        return [
            "price" => $price ?? 0,
        ];
    }
}
