<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\Models;

use Boot\Models\Model;
use Sales\Models\Sales;
use Supply\Models\SupplyOrder;

class BankPay extends Model
{
    protected $fillable = ["bank_detail_id", "pid", "sum"];

    public function bank_detail()
    {
        return $this->belongsTo(BankDetail::class, "bank_detail_id");
    }

    static function getTotal($module, $pid)
    {
        return static::whereHas("bank_detail", function ($query) use ($module) {
            $query->where("module", $module);
        })->where("pid", $pid)->sum("sum");
    }

    static function getTotalDetail($bank_detail_id)
    {
        return static::where("bank_detail_id", $bank_detail_id)->sum("sum");
    }

    static function getPayStatus($total, $total_pay)
    {
        if ($total == 0 || $total_pay == 0) {
            return 0;
        } else if ($total == $total_pay || $total_pay > $total) {
            return 2;
        } else {
            return 1;
        }
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, "pid");
    }

    public function supply_order()
    {
        return $this->belongsTo(SupplyOrder::class, "pid");
    }
}
