<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Получите каналы, на которых должно транслироваться мероприятие.
     *
     * @return \Boot\System\Broadcasting\Channel|\Boot\System\Broadcasting\Channel[]|string[]|string
     */
    public function broadcastOn();
}
