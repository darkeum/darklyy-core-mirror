<?php

namespace Logistics\Model;

// RULES
// - Статусы: 1 - ждёт забора; 2 - забрана; 3 - не забрана;
// - receipts: null - не поступал;
class LogisticsDriverDetail extends \Model\Model{
    protected $fillable = ["driver", "logistics_detail_id", "count_save", "count", "status", "receipts"];

    public function logistics_detail(){
        return $this->belongsTo(\Logistics\Model\LogisticsDetail::class, "logistics_detail_id");
    }
}