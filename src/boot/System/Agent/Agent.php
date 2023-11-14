<?php 

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Agent class
*/

namespace Boot\System\Agent;

class Agent {
    protected $agent;
    protected $sxgeo;
    protected $remote;

    //конструктор класса 
    public function __construct(
        \Jenssegers\Agent\Agent $agent = null, 
        \Boot\System\Agent\SxGeo $sxgeo = null,
        \Boot\System\Agent\RemoteAddress $remote = null) {
        //подключение вспомогательных классов
        $this->agent = $agent ? $agent : new \Jenssegers\Agent\Agent();
        $this->sxgeo = $sxgeo ?: new \Boot\System\Agent\SxGeo(ROOT.'boot/System/Agent/SxGeo/SxGeoCity.dat');
        $this->remote = $remote ?: new \Boot\System\Agent\RemoteAddress;
    }

    //получение языка
    public function getLanguages() {
        return $this->agent->languages();
    }
   
    //получение браузера
    public function getBrowser() {
        $result = array();
        $result['name'] = $this->agent->browser();
        $result['version'] = $this->agent->version($result['name']);
        return $result;
    }
    
    //получение операционной системы
    public function getPlatform() {
        $result = array();
        $result['name'] = $this->agent->platform();
        $result['version'] = $this->agent->version($result['name']);
        return $result;
    }

    //получение имя устройства (если оно мобильное)
    public function getDevice() {
        return $this->agent->device();        
    }

    //получение данных бота если клиент таковым является
    public function getRobot(){
        return $this->agent->robot();   
    }
    
    //проверка на пк
    public function isDesktop() {
        return $this->agent->isDesktop();         
    }
    
    //проверка на телефон
    public function isPhone(){
        return $this->agent->isPhone();  
    }

    //проверка на бота (робота)
    public function isRobot() {
        return $this->agent->isRobot();   
    }
    
    //получение типа устройства
    public function getDeviceType() {
        return $this->agent->deviceType();         
    }

    //получение ip адреса
    public function getIp() {
        return $this->remote->get_ip_address();         
    }

    //проверка ip адреса
    public function isIp($ip = null) {
        return $this->remote->validate_ip(empty($ip) ? $this->getIp() : $ip);         
    }

    //получение данных по геолокации
    public function getGeo($ip = null) {       
        return $this->sxgeo->get(empty($ip) ? $this->getIp() : $ip);         
    }   

    //сводный массив данных пользователя
    public function getAll() {  
        $resultArray = array(
            'isIp' => $this->isIp(),
            'ip' => $this->getIp(),
            'geo' => $this->getGeo(),
            'language' => $this->getLanguages(),
            'deviceType' => $this->getDeviceType(),
            'isRobot' => $this->isRobot(),
            'isPhone' => $this->isPhone(),
            'isDesktop' => $this->isDesktop(),
            'device' => $this->getDevice(),
            'platform' => $this->getPlatform(),
            'browser' => $this->getBrowser(),
            'robot' => $this->getRobot(),
        );
        return $resultArray;         
    }
}