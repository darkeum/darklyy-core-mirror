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

interface EventMutex
{
    /**
     * Попытка получить mutex события для указанного события.
     *
     * @param  \Boot\System\Scheduling\Event  $event
     * @return bool
     */
    public function create(Event $event);

    /**
     * Определите, существует ли mutex события для данного события.
     *
     * @param  \Boot\System\Scheduling\Event  $event
     * @return bool
     */
    public function exists(Event $event);

    /**
     * Очистить mutex события для указанного события.
     *
     * @param  \Boot\System\Scheduling\Event  $event
     * @return void
     */
    public function forget(Event $event);
}
