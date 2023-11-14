<?php

namespace Catalog\Controller;

use \Boot\System\Http\Request;
use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;

class CatalogAttributeValueController extends AbstractController{
    static function store(Request $request){
        $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::create($request->all());
        return response($catalog_attribute_value);
    }

    static function update(Request $request, $id){
        $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::findOrFail($id);
        $catalog_attribute_value->update($request->all());
        \Catalog\Jobs\CatalogAttributeChangeValueJob::dispatch($catalog_attribute_value);
        return response($catalog_attribute_value);
    } 

    static function mass_update(Request $request){
        $items = $request->get("items");
        $new_items = [];
        foreach($items as $item){
            $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::find($item["id"]);
            $catalog_attribute_value->update(["position" => $item["position"]]);
            $new_items[] = $catalog_attribute_value;
        }
        return response($new_items);
    }

    static function destroy($id){
        $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::findOrFail($id);
        $catalog_attribute_value->delete();
        \Catalog\Jobs\CatalogAttributeChangeValueJob::dispatch($catalog_attribute_value);
        return response($catalog_attribute_value);
    }
}