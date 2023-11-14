<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Auth Helper class
*/

namespace Boot\Support\Facades;

use \Boot\Services\Agent as AgentServices;

class Agent
{
    /**
     * Получение IP пользователя
     *   
     * @return bool    Возвращает IP пользователя если IP определить не удалось, возвращает false
     */
    public static function getIp()
    {
        $agent = new AgentServices;    
        return $agent->getIp();
    }
    
}
