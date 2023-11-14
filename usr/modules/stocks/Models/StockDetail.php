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
use Stocks\Models\Stock;

class StockDetail extends Model
{
    protected $fillable = ["stock", "cid", "sum", "status"];

    public function stock_()
    {
        return $this->belongsTo(Stock::class, "stock");
    }
}
