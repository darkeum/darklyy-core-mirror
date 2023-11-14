<?php

namespace Boiler\Model;

class BoilerBidDataDetail extends \Boot\Models\Model{
    protected $fillable = ["bbd_id", "cid", "s_sku", "s_name", "price_type", "s_price", "s_price_local", "s_unit", "count", "discount", "discount_sum", "type_nds", "nds", "total", "date_post", "position", "comment", "checkbox"];

    public function catalog()
    {
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, "cid");
    }
}