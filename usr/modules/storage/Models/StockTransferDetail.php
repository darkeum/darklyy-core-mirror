<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Storage\Models;

use Boot\Models\Model;

class StockTransferDetail extends Model
{
    protected $fillable = ["transfer_id", "cid", "sdid", "s_name", "s_sku", "s_unit", "s_price", "count", "status"];

    public function stock_transfer()
    {
        return $this->belongsTo(\Storage\Models\StockTransfer::class, 'transfer_id');
    }
}
