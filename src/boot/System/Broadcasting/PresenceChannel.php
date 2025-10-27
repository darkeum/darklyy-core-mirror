<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Broadcasting;

class PresenceChannel extends Channel
{
    /**
     * Создайте новый экземпляр канала.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct('presence-'.$name);
    }
}
