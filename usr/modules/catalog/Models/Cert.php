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

class Cert extends Model
{
    protected $fillable = ["aid", "uid", "type", "date_add", "date_edit", "date_start", "date_end", "issue", "numb", "comment", "status"];
}
