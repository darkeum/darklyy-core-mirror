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

class Hogart extends AbstractParser
{

    protected $personal_area = true;

    public function get_token()
    {
        $client = static::client();
        $data = $client->request("POST", "https://b2b.hogart.ru/api/v1/user/login", [
            "form_params" => [
                "password" => $this->get_password(),
                "phone" => $this->get_login(),
            ],
        ]);
        $data = json_decode($data->getBody(), true);
        return $data["token"];
    }

    public function parse()
    {
        $token = $this->get_token();

        $url_d = parse_url($this->url);
        parse_str($url_d['query'], $params);
        $client = static::client();
        $data = $client->request("GET", "https://b2b.hogart.ru/api/v1/product/" . $params["product"], [
            "cookies" => static::CookieJar("https://b2b.hogart.ru", "_ga=GA1.2.1894014043.1609142282; _ym_uid=1609142282785865309; _ym_d=1609142282; _gid=GA1.2.1975684800.1613375714; _ym_isad=1; _ym_visorc=w; _b2b_user_token={$token}; _gat=1; _csrf=v12eCWDe1wcZrZVz3hzYUWp-LEZSDvmb"),
            "headers" => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        $json = $data->getBody();
        try {
            $data = json_decode($json, true);
        } catch (\Exception $e) {
            $data = [];
        }
        $new_data = [
            "price" => isset($data["price"]["value"]) ? $data["price"]["value"] : 0,
            "purchase" => isset($data["contractPrice"]) ? $data["contractPrice"] : 0,
        ];
        return $new_data;
    }
}
