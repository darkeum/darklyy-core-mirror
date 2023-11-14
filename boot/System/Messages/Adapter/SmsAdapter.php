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

class SmsAdapter extends AbstractAdapter
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
        // if(Config::has('messages.smsru')){

        // }
        // print_r($this->phone);
        // print_r('dddd');
        $smsru = new \SMSRU('40316a89-2477-4d94-6592-5b59379386d7');
        $data = new \stdClass();
        $data->to = $this->phone;
        $data->text = $this->text . ' ' . $this->url ?? '';
        $sms = $smsru->send_one($data);
       
    }
}
