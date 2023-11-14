<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Messages class
*/


namespace Boot\System\Messages\Adapter;

use \Boot\Support\Facades\Config;
use Telegram\Bot\Api;



class TelegramAdapter extends AbstractAdapter
{
    protected $title = null;
    protected $text = null;
    protected $url = null;
    protected $uid = null;
    protected $phone = null;

    public function __construct($parameters){
        // print_r($parameters->parameterSms);
        $this->title = $parameters->title ?? null;
        $this->text = $parameters->text ?? null;
        $this->url = $parameters->url ?? null;
        $this->uid = $parameters->uid ?? null;
        $this->phone = $parameters->parameterSms['phone'] ?? null;
    }

    public function checkParameters()
    {
    }

    public function getParameters()
    {
    }

    public function send()
    {
        $telegram = new Api('1127443797:AAGJHKDxD1N9ga7N4GYMX1Et9JSj8Ea2_Ds');   
        $response = $telegram->getMe();

$botId = $response->getId();
$firstName = $response->getFirstName();
$username = $response->getUsername();
dd();
    }
}
