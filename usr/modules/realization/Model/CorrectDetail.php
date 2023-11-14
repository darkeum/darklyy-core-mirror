<?php

namespace Realization\Model;

//RULES
// 1. Удаление: разрешено если корректировка не проведена и нет корректировки на корректировку.
class CorrectDetail extends \Model\Model{
    protected $fillable = ["correct_id", "pid_detail", "realization_detail_id", "s_price", "count", "s_price_type", "discount", "discount_sum", "nds", "nds_type", "total"];

    public function correct()
    {
        return $this->belongsTo(\Realization\Model\Correct::class);
    }
}