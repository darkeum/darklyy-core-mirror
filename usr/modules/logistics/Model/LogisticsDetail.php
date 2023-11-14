<?php

namespace Logistics\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

// RULES
// - Не добавлять позиции, если такой sod_id уже есть.
// - Количество: если count = NULL, то количество берется из счёта поставщика, иначе count
// - Статусы: 1 - позиция в списке; 2 - позиция отправлена водителю; 3 - позиция забрана; 4 - позиция с проблемой; 5 - позиция приехала до распределения; 6 - позиция не забрана. 
// - Если водитель не привёз товар, то статус уст. 3.
// - Если водитель привёз не весь товар, то статус уст. 1 и количество уст. сколько не привезли
class LogisticsDetail extends \Model\Model{
    use SoftDeletes;

    protected $fillable = ["sod_id", "count", "status", "transport_companies"];

    public function supply_order_detail(){
        return $this->belongsTo(\Supply\Model\SupplyOrderDetail::class, "sod_id");
    }
}