<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Stocks\Models;

use Boot\Models\Model;
use Boot\Traits\CacheAll;

class Stock extends Model
{
    use CacheAll;
    
    protected $fillable = ["org", "name", "position", "status"];
}
