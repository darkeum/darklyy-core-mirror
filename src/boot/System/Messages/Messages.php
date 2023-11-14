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


namespace Boot\System\Messages;

use Boot\System\Messages\Adapter\SmsAdapter;
use Boot\System\Messages\Adapter\EmailAdapter;

class Messages {

    public $sendViber = false;
    public $sendVk = false;
    public $sendEmail = false;
    public $sendPush = false;
    public $sendApp = false;
    public $sendTelegram = false;
    public $sendSms = false;
    public $sendNotif = false;

    public $parameterSms = [];

    public $title = null;
    public $text = null;
    public $url = null;
    public $module = null;
    public $pid = null;
    public $icon = null; 
    public $uid = null;

    public $daemon = true;

    public function setTitle($title){
        $this->title = $title;
        return $this;
    }

    public function setUrl($url){
        $this->url = $url;
        return $this;
    }
    
    public function setModule($module){
        $this->module = $module;
        return $this;
    }

    public function setPid($pid){
        $this->pid = $pid;
        return $this;
    }

    public function setIcon($icon){
        $this->icon = $icon;
        return $this;
    }
    
    public function setText($text){
        $this->text = $text;
        return $this;
    }

    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    public function setMessageParameters($parameters){
        $this->uid = $parameters['uid'] ?? null;
        $this->module = $parameters['module'] ?? null;
        $this->pid = $parameters['pid'] ?? null;
        $this->title = $parameters['title'] ?? null;
        $this->text = $parameters['text'] ?? null;
        $this->icon = $parameters['icon'] ?? null;
        $this->url = $parameters['url'] ?? null;
        return $this;
    }

    public function setViber($flag = true){
        $this->sendViber = $flag;
        return $this;
    }

    public  function setVk($flag = true){
        $this->sendVk = $flag;
        return $this;
    }

    public  function setSms($flag = true){
        $this->sendSms = $flag;
        return $this;
    }

    public  function setEmail($flag = true){
        $this->sendEmail = $flag;
        return $this;
    }

    public  function setSmsParameters($phone){
        $this->parameterSms = $phone;
        return $this;
    }

    public  function setEmailParameters($email){
        $this->parameterEmail = $email;
        return $this;
    }

    public function getParametr() {
        return [
            'parameterSms' => $this->parameterSms,
        
        ];

    }
    
    public function send(){
        if($this->sendSms){
            $adapter = new SmsAdapter($this);
            $adapter->send();
        }

        if($this->sendEmail){
            $adapter = new EmailAdapter($this);
            $adapter->send();
        }

    }
}