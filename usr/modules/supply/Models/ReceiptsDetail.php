<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Supply\Models;

use Boot\Models\Model;
use Supply\Models\Receipts;
use Sales\Model\SalesDetail;
use Supply\Models\SupplyOrderDetail;

class ReceiptsDetail extends Model
{

    protected $fillable = ["rid", "cid", "sales_detail_id", "supply_detail_id", "name", "sku", "unit", "unit_convert", "count", "count_convert", "price", "total", "type_nds", "nds", "comment", "status"];

    public function receipts()
    {
        return $this->belongsTo(Receipts::class, "rid");
    }

    public function supply_order_detail()
    {
        return $this->belongsTo(SupplyOrderDetail::class, "supply_detail_id");
    }

    public function sales_detail()
    {
        return $this->belongsTo(SalesDetail::class, "sales_detail_id");
    }
}
