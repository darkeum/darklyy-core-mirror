<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Catalog\Models;

use Boot\Models\Model;

class CertType extends Model
{
    protected $fillable = ["title", "print", "status", "date_start", "date_end"];

    protected $casts = [
        'date_start' => "boolean",
        'date_end' => "boolean",
    ];
}
