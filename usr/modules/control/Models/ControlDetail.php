<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Control\Models;

use Boot\Models\Model;

class ControlDetail extends Model
{
    protected $fillable = ["control_id", "s_name", "s_sku", "cid", "sod_id", "sdid", "count", "price_now", "comment", "status"];

    public function control()
    {
        return $this->belongsTo(\Control\Model\Control::class, "control_id");
    }

    public function catalog()
    {
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, "cid");
    }

    public function supply_order_detail()
    {
        return $this->belongsTo(\Supply\Model\SupplyOrderDetail::class, "sod_id");
    }

    public function sales_detail()
    {
        return $this->belongsTo(\Sales\Model\SalesDetail::class, "sdid");
    }
}
