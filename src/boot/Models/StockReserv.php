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

use \Illuminate\Database\Eloquent\SoftDeletes;

/**
 @deprecated Используйте Reserve\Support\StockReserve
 */
class StockReserv extends Model
{
    use SoftDeletes;

    protected $fillable = ["cid", "stock_id", "uid", "module", "pid", "count", "date", "status"];
}
