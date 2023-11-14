<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

class Log extends \Boot\Models\Model
{
    protected $connection = 'LOG';

    public $timestamps = false;

    protected $fillable = ["type", "uid", "module", "pid", "date", "comment", "agent", "ip", "browser", "os", "status"];
}
