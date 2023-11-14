<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\Models;

use Boot\Models\Model;

class Notifications extends Model {
    protected $table = 'notif';
    protected $fillable = ["uid", "module", "pid", "url", "text", "title", "icon", "date", "read_date", "status"];
}