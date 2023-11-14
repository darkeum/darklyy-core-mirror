<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Inventory\Models;

use Boot\Models\Model;
use Boot\Models\Users;

class InventoryCheck extends Model
{
    protected $fillable = ["iid", "uid", "date", "status"];

    public function users()
    {
        return $this->belongsTo(Users::class, "uid");
    }
}
