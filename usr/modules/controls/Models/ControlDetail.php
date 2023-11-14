<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (24.07.2023 15:22)
*/

namespace Controls\Models;

use Catalog\Models\Catalog;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Model\Model;
use Supply\Models\SupplyOrderDetail;

class ControlDetail extends Model
{
    protected $fillable = ["control_id", "s_name", "s_sku", "cid", "sod_id", "sdid", "count", "price_now", "comment", "status"];


    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class, "control_id");
    }

    function catalog(): HasOne {
        return $this->hasOne(Catalog::class, 'id','cid');
    }

    public function supply_order_detail(): BelongsTo
    {
        return $this->belongsTo(SupplyOrderDetail::class, "sod_id");
    }

    public function sales_detail(): BelongsTo
    {
        return $this->belongsTo(\Sales\Models\SalesDetail::class, "sdid");
    }
}