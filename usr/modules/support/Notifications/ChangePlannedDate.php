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

use Boot\Support\Carbon;
use Support\Notifications\SupportNotification;


class ChangePlannedDate extends SupportNotification
{
    public function setText()
    {
        $this->text = "Изменения в заявке на обслуживание *№{$this->support->id}.*\nПланируемая дата выполнения заявки изменена на: " . Carbon::parse($this->support->planned_at)->format('d.m.Y H:i');
    }
}
