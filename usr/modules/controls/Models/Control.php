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

use Boot\Models\Users;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Model\Model;

class Control extends Model
{
    protected $fillable = ["uid", "date", "supply_o", "sales_id", "total", "plan_pay_date", "pay", "pay_date", "pay_num", "pay_uid", "pay_total", "oid", "object", "comment", "reason", "status"];

    public function control_detail(): HasMany
    {
        return $this->hasMany(ControlDetail::class, "control_id");
    }

    public function supply_order(): BelongsTo
    {
        return $this->belongsTo(\Supply\Models\SupplyOrder::class, "supply_o");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, "uid");
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(\Sales\Models\Sales::class, "sales_id");
    }

    static function getStatus(): array
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

    static function getPay(): array
    {
        return [
            0 => "Ожидает оплаты",
            1 => "Оплачено",
            2 => "Отмена оплаты",
            3 => "Частичная оплаты",
        ];
    }

    static function getStatusPay(): array
    {
        return [
            0 => 'Ожидает оплаты',
            1 => 'Оплачен',
            2 => 'Не оплачен',
            3 => 'Частичная оплата',
        ];
    }
    public function getPayViewAttribute()
    {
        return static::getPay()[$this->pay] ?? $this->pay;
    }

    static function re_total($id): void
    {
        $total = ControlDetail::where("control_id", $id)->sum("total");
        static::where("id", $id)->update(["total" => $total]);
    }
}