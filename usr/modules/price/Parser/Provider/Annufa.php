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

class Annufa extends AbstractParser
{

    public function parse()
    {
        $client = static::client();
        $data = $client->request("GET", $this->url);
        preg_match_all('|<script type="application/ld\+json">(.*)</script>|Uis', $data->getBody(), $matches);
        if (isset($matches[1][0])) {
            try {
                $data = json_decode($matches[1][0], true);
                $price = empty($data["offers"][0]["price"]) ? 0 : $data["offers"][0]["price"];
                $price = $this->clear_price($price);
            } catch (\Exception $e) {
                $price = 0;
            }
        } else {
            $price = 0;
        }

        return [
            "price" => $price,
        ];
    }
}
