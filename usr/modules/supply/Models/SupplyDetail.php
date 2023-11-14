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
use Supply\Models\Supply;
use Sales\Models\SalesDetail;
use Supply\Models\SupplyOrderDetail;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyDetail extends Model
{
    use SoftDeletes;

    protected $fillable = ["sid", "sdid", "cid", "s_name", "s_sku", "price_type", "s_price", "s_price_all", "sos_price", "sos_price_date", "sos_bill_aid", "oid", "s_unit", "count", "count_send", "comment", "date_post", "date_ship", "status"];

    public function supply()
    {
        return $this->belongsTo(Supply::class, "sid");
    }

    public function sales_detail()
    {
        return $this->belongsTo(SalesDetail::class, "sdid");
    }

    static function check_can_delete($id)
    {
        //check supply order
        $supply_order_detail = SupplyOrderDetail::where("supply_d", $id)->first();
        if (!empty($supply_order_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите позицию из счёта поставщика!",
                "link" => "/administration/module/supply/order_detail/{$supply_order_detail->supply_o}"
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}
