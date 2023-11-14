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

class Ipro extends AbstractParser
{

    protected $log = "470027099akv";
    protected $pwd = false;
    protected $city = "18";
    protected $user_id = "7d7240e3-2e51-420a-9f4e-0e6dcbd960e8";

    protected $personal_area = true;

    public function get_password()
    {
        if (!$this->pwd) {
            $password = parent::get_password();
            $this->pwd = $password;
        }
    }

    public function parse()
    {
        $url = $this->url;
        $u = explode("/", $url);
        $id = $u[5];

        $this->get_password();

        $host = $this->get_host();
        //авторизация
        $client = static::client();
        // $client = new \GuzzleHttp\Client([
        //     "cookies" => true,
        //     "verify" => false,
        //     "proxy" => "HTTPS://82.162.210.99:8080",
        //     'connect_timeout' => 20
        // ]);
        $data = $client->request("POST", "https://{$host}/api/ipro/user/login?log={$this->get_login()}&pwd={$this->pwd}", [
            "headers" => [
                "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36",
            ],
        ]);

        $data = json_decode($data->getBody(), true);
        if (empty($data["data"]["session"])) {
            throw new \Exception(AbstractParser::get_text_catalog_error("Не найден `session_id` обратитесь к администратору!", $this->catalog));
        } else {
            $session_id = $data["data"]["session"];
        }

        //проверка захода
        // $this->url = "https://ipro.etm.ru/api/ipro/user/profile?session-id={$session_id}";
        // $json = $this->get_content();

        //взятие цены
        $data = $client->request("GET", "https://{$host}/api/ipro/goods/{$id}?session-id={$session_id}", [
            "headers" => [
                "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36",
            ],
        ]);
        $data = json_decode($data->getBody(), true);
        return [
            "price" => isset($data["data"]["gdsPrice2"]) ? $this->clear_price(str_replace(",", "", $data["data"]["gdsPrice2"])) : 0,
            "purchase" => isset($data["data"]["gdsPrice1"]) ? $this->clear_price(str_replace(",", "", $data["data"]["gdsPrice1"])) : 0,
        ];
    }
}
