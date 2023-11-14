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

class Shipment extends Model
{
        protected $fillable = ["cid", "module", "pid", "uid", "stock_id", "initial_value", "count", "sum", "date", "comment"];
}
