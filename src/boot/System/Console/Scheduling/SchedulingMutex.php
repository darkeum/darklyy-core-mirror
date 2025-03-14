<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Scheduling;

use DateTimeInterface;

interface SchedulingMutex
{
    /**
     * Попытка получить и запланировать mutex для указанного события.
     *
     * @param  \Boot\System\Scheduling\Event  $event
     * @param  \DateTimeInterface  $time
     * @return bool
     */
    public function create(Event $event, DateTimeInterface $time);

    /**
     * Определите, существует ли mutex планирования для данного события.
     *
     * @param  \Boot\System\Scheduling\Event  $event
     * @param  \DateTimeInterface  $time
     * @return bool
     */
    public function exists(Event $event, DateTimeInterface $time);
}
