<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Models;

use Boot\Models\Model;

class Log extends Model
{
    protected $connection = 'atrienergo_price';

    protected $fillable = ["module", "pid", "comment"];

    protected $casts = [
        'comment' => "object",
    ];
}
