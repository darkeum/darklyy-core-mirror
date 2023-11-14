<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Orgs\Models;

use Boot\Models\Model;

class ChartAccounts extends Model
{
    protected $fillable = ["code", "title", "status"];
}
