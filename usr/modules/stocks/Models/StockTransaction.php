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
use Catalog\Models\Catalog;

class StockTransaction extends Model
{
    protected $fillable = ["cid", "stock_id", "uid", "initial_value", "sign", "count", "sum", "date", "module", "pid", "comment", "status"];

    public function catalog()
    {
        return $this->hasOne(Catalog::class, "cid");
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, "stock_id");
    }
}
