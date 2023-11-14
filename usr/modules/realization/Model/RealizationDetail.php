<?php

namespace Realization\Model;

class RealizationDetail extends \Model\Model{
    protected $fillable = ["rid", "position", "cid", "sdid", "s_name", "s_sku", "supply_order_price", "s_price", "s_price_type", "s_unit", "count", "discount", "discount_sum", "nds", "nds_type", "total", "status"];

    public function realization(){
        return $this->belongsTo(\Realization\Model\Realization::class, "rid");
    }

    public function catalog(){
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, "cid");
    }

    public function sales_detail(){
        return $this->belongsTo(\Sales\Model\SalesDetail::class, "sdid");
    }
}