<?php

namespace Catalog\Model;

class CatalogAttributeCategory extends \Boot\System\Database\Model\Model{
    protected $fillable = ["attribute_id", "category_id", "hidden", "position"];

    public function category()
    {
        return $this->belongsTo(\Catalog_v2\Model\Categories::class, "category_id");
    }
}