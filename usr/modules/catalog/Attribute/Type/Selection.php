<?php

namespace Catalog\Attribute\Type;

use Catalog\Attribute\Attribute;

class Selection extends Attribute{
    static function templatePatch(){
        return "@catalog/manager/attribute/type/selection";
    }

    static function getRus(){
        return "Подставные";
    }

    static function getValues($item):Object {
        return (object)[
            "value" => $item->attribute_value->value,
            "value_save" => $item->attribute_value->value_save,
        ];
    }

    static function getStandardData($attribute){
        $data = [];
        $attribute->attribute_relation()->lazyById()->each(function($item) use (&$data){
            $data[] = (object)[
                "catalog_attribute_relation" => $item,
                "value" => $item->attribute_value->value,
            ];
        });
        return $data;
    }

    static function setStandardData($attribute, $data){
        foreach($data as $item){
            //создаем catalog_attribute_value
            $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::create(["attribute_id" => $attribute->id, "value" => $item->value]);
            //прописываем связь в catalog_attribute_relation
            $item->catalog_attribute_relation->update(["attribute_value_id" => $catalog_attribute_value->id]);
        }
    }

    static function clearStandardData($data){
        foreach($data as $item){
            //получаем catalog_attribute_value
            $attribute_value = $item->catalog_attribute_relation->attribute_value;
            //удаляем связь 
            $item->catalog_attribute_relation->update(["attribute_value_id" => null]);
            //удаляем catalog_attribute_value
            $attribute_value->forceDelete();
        }
    }

    static function delete($catalog_attribute){
        $catalog_attribute->attribute_value()->forceDelete();
    }

    static function checkAttributeValue(\Catalog\Model\CatalogAttribute $attribute, $attribute_value){
        if(\Catalog\Model\CatalogAttributeValue::where("attribute_id", $attribute->id)->where("value", $attribute_value)->exists()){
            return true;
        } else {
            throw new \Exception("Значение атрибута id: {$attribute->id} и значением {$attribute_value} не найден");
        }
    }

    static function updateOrCreate(\Catalog_v2\Model\Catalog $catalog, \Catalog\Model\CatalogAttribute $attribute, $attribute_value){
        $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::where("attribute_id", $attribute->id)->where("value",  $attribute_value)->first();
        return \Catalog\Model\CatalogAttributeRelation::updateOrCreate(
            ["attribute_id" => $attribute->id, "cid" => $catalog->id],
            ["attribute_value_id" => $catalog_attribute_value->id],
        );
    }
}