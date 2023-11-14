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
use Controls\Models\ControlDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sales\Models\SalesDetail;

class SupplyOrderDetail extends Model
{
    use SoftDeletes;

    protected $fillable = ["supply_o", "cid", "sale_detail_id", "supply_d", "supply_s", "s_sku", "s_name", "count", "count_receipts", "comment", "price_now", "total", "type_nds", "nds", "unit", "date_ship", "date_end", "status"];

    public function supply_order()
    {
        return $this->belongsTo(SupplyOrder::class, "supply_o");
    }

    public function supply_detail()
    {
        return $this->belongsTo(SupplyDetail::class, "supply_d");
    }

    public function receipts_detail()
    {
        return $this->hasMany(ReceiptsDetail::class, "supply_detail_id");
    }

    static function check_can_delete($id)
    {
        //check control
        $control_detail = ControlDetail::where("sod_id", $id)->whereHas(function ($q) {
            $q->where("status", "!=", 2);
        })->first();
        if (!empty($control_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите позицию из контроля закупки!",
                "link" => "/administration/module/control/{$control_detail->control_id}"
            ], JSON_UNESCAPED_UNICODE));
        }

        //check receipts
        $receipts_detail = ReceiptsDetail::where("supply_detail_id", $id)->first();
        if (!empty($receipts_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите позицию из документа поступления!",
                "link" => "/administration/module/supply/receipts_detail/{$receipts_detail->rid}"
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    function sales_detail(): HasOne
    {
        return $this->hasOne(SalesDetail::class, 'id', 'sale_detail_id');
    }
}
