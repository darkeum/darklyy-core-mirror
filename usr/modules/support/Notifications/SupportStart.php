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

class SupportStart extends SupportNotification
{
    public function setText()
    {
        $this->text = "Ваша заявка на обслуживание №{$this->support->id} принята в работу";
    }
}
