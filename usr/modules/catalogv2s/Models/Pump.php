<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Catalogv2s\Models;

use Boot\Models\Model;

class Pump extends Model
{
    protected $fillable = ["deleted_at","cid","uid","uid_change","data",
    "max_x","max_y","mix_x","min_y","type_of_instalation",
    "work_line","type","priority",
    "created_at","updated_at"
];


}