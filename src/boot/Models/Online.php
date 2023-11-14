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

class Online extends Model
{
    protected $fillable = ["uid", "ip", "group", "time_marker", "name", "agent", "url"];
    public $timestamps = false;
    protected $casts = [
        'time_marker' => "datetime",
        'agent' => "array",
    ];
}
