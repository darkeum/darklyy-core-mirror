<?php

namespace Catalog_v2\Model;

class CatalogGroupsDiscountName extends \Model\Model{

    protected $fillable = ["name", "position"];

    public function catalog_groups_discount(){
        return $this->hasMany(CatalogGroupsDiscount::class, "catalog_groups_discount_name_id");
    }
}