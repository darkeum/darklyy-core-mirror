<?php

namespace Catalog\Attribute\Type;

use Catalog\Attribute\Attribute;

class Manual extends Attribute{
    static function templatePatch(){
        return "@catalog/manager/attribute/type/manual";
    }

    static function getRus(){
        return "Вводимые";
    }

    static function getValues($item):Object {
        return (object)[
            "value" => $item->value_text,
            "value_save" => $item->value_text,
        ];
    }

    static function getStandardData($attribute){
        $data = [];
        $attribute->attribute_relation()->lazyById()->each(function($item) use (&$data){
            $data[] = (object)[
                "catalog_attribute_relation" => $item,
                "value" => $item->value_text,
            ];
        });
        return $data;
    }

    static function setStandardData($attribute, $data){
        foreach($data as $item){
            //прописываем значение в catalog_attribute_relation
            $item->catalog_attribute_relation->update(["value_text" => $item->value]);
        }
    }

    static function clearStandardData($data){
        foreach($data as $item){
            //удаляем значение 
            $item->catalog_attribute_relation->update(["value_text" => null]);
        }
    }

    static function delete($catalog_attribute){
        
    }

    static function checkAttributeValue(\Catalog\Model\CatalogAttribute $attribute, $attribute_value){
        return true;
    }

    static function updateOrCreate(\Catalog_v2\Model\Catalog $catalog, \Catalog\Model\CatalogAttribute $attribute, $attribute_value){
        return \Catalog\Model\CatalogAttributeRelation::updateOrCreate(
            ["attribute_id" => $attribute->id, "cid" => $catalog->id],
            ["value_text" => $attribute_value],
        );
    }
}