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

interface HasBroadcastChannel
{
    /**
     * Получите определение маршрута широковещательного канала, связанное с данным объектом.
     *
     * @return string
     */
    public function broadcastChannelRoute();

    /**
     * Получите имя канала вещания, связанное с данным объектом.
     *
     * @return string
     */
    public function broadcastChannel();
}
