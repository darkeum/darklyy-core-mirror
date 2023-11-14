<?php

namespace Catalog\Model;

class CatalogAttributeRelation extends \Boot\System\Database\Model\Model{
    protected $fillable = ["value_text", "attribute_id", "cid", "attribute_value_id"];

    public function catalog(){
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, "cid");
    }

    public function attribute(){
        return $this->belongsTo(\Catalog\Model\CatalogAttribute::class, "attribute_id");
    }

    public function attribute_value(){
        return $this->belongsTo(\Catalog\Model\CatalogAttributeValue::class, "attribute_value_id");
    }
}