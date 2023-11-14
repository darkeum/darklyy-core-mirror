<?php

namespace Request\Support;

class RequestSupport{
    static function catalogPriceRival($cid){
        return \Price\Models\CatalogPriceRival::firstOrCreate([
            "id" => $cid,
            "type" => 1,
            "our_supplier_discount" => 50,
            "price_adjustments" => 0,
            "min_margin" => 0,
            "discount_installers_mrc" => 42,
        ]);
    }
}