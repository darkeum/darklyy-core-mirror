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
use Boot\Traits\CacheAll;

class CatalogPriceType extends Model
{
    use CacheAll;

    protected $fillable = ["title", "currency", "parent", "course", "course_auto", "hidden", "type", "link_id"];

    static function getType()
    {
        return [
            "PURCHASE" => __('price::types.grid.type.purchase'),
            "PRICE"    => __('price::types.grid.type.price'),
            "OTHER"    => __('price::types.grid.type.other'),
        ];
    }

    public function getTypeViewAttribute()
    {
        return static::getType()[$this->type] ?? $this->type;
    }

    public function link()
    {
        return $this->hasOne(CatalogPriceType::class, "link_id");
    }
}
