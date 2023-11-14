<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Models;

use Boot\Models\Model;
use Price\Models\CatalogPriceType;
use Price\Models\CatalogPriceDetail;

class CatalogPriceTemp extends Model
{
    protected $fillable = ["id", "price_purchase", "price_type_purchase", "price_price", "price_type_price"];

    public function catalog_price_detail_purchase()
    {
        return $this->belongsTo(CatalogPriceDetail::class, "price_purchase");
    }
    public function catalog_price_detail_price()
    {
        return $this->belongsTo(CatalogPriceDetail::class, "price_price");
    }
    public function catalog_price_type_purchase()
    {
        return $this->belongsTo(CatalogPriceType::class, "price_type_purchase");
    }
    public function catalog_price_type_price()
    {
        return $this->belongsTo(CatalogPriceType::class, "price_type_price");
    }
}
