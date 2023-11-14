<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\Notifications;

use Support\Notifications\SupportNotification;

class SupportNewMessage extends SupportNotification
{
    public function setText()
    {
        $this->text = "Новое сообщение в заявке на обслуживание №{$this->support->id}";
    }
}
