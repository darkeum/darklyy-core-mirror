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
use Catalog\Models\Catalog;

class CatalogPriceDetail extends Model
{

    protected $fillable = ["price_id", "cid", "sum"];

    public function price()
    {
        return $this->belongsTo(CatalogPrice::class);
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'cid');
    }
}
