<?php

namespace Catalog\Attribute;

abstract class Attribute{
    abstract static function templatePatch();
    abstract static function getRus();
    abstract static function getValues($item):Object;
    
    /*
        [
            (object)[
                "catalog_attribute_relation" => "model",
                "value" => "value",
            ]
        ]
    */
    abstract static function getStandardData(\Catalog\Model\CatalogAttribute $attribute);

    abstract static function setStandardData(\Catalog\Model\CatalogAttribute $attribute, $data);
    abstract static function clearStandardData($data);
    abstract static function delete(\Catalog\Model\CatalogAttribute $catalog_attribute);
    abstract static function checkAttributeValue(\Catalog\Model\CatalogAttribute $attribute, $attribute_value);
    abstract static function updateOrCreate(\Catalog_v2\Model\Catalog $catalog, \Catalog\Model\CatalogAttribute $attribute, $attribute_value);

    static function getClassByType($type){
        $attribute_type = static::getTypes();
        $class = $attribute_type->{$type};
        return $class;
    }

    static function getTypes():Object{
        return (object)[
            "selection" => \Catalog\Attribute\Type\Selection::class,
            "manual" => \Catalog\Attribute\Type\Manual::class,
        ];
    }

    static function deleteRelation(\Catalog_v2\Model\Catalog $catalog, \Catalog\Model\CatalogAttribute $attribute){
        return \Catalog\Model\CatalogAttributeRelation::where("attribute_id", $attribute->id)->where("cid", $catalog->id)->delete();
    }
}