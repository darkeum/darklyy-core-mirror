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

class Control extends Model
{
    protected $fillable = ["uid", "date", "supply_o", "sales_id", "total", "plan_pay_date", "pay", "pay_date", "pay_num", "pay_uid", "pay_total", "oid", "object", "comment", "reason", "status"];

    public function control_detail()
    {
        return $this->hasMany(\Control\Model\ControlDetail::class, "control_id");
    }

    public function supply_order()
    {
        return $this->belongsTo(\Supply\Model\SupplyOrder::class, "supply_o");
    }

    public function user()
    {
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }

    public function sales()
    {
        return $this->belongsTo(\Sales\Model\Sales::class, "sales_id");
    }

    static function getStatus()
    {
        return [
            0 => "Ожидает отправки на проверку",
            1 => "На согласовании",
            2 => "Отправлен на доработку",
            3 => "Подтвержден",
        ];
    }

    public function getStatusViewAttribute()
    {
        return static::getStatus()[$this->status] ?? $this->status;
    }

    static function getPay()
    {
        return [
            0 => "Ожидает оплаты",
            1 => "Оплачено",
            2 => "Отмена оплаты",
            3 => "Частичная оплаты",
        ];
    }

    public function getPayViewAttribute()
    {
        return static::getPay()[$this->pay] ?? $this->pay;
    }

    static function re_total($id)
    {
        $total = \Control\Model\ControlDetail::where("control_id", $id)->sum("total");
        static::where("id", $id)->update(["total" => $total]);
    }
}
