<?php

namespace Catalog\Controller;

use \Boot\System\Http\Request;
use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;

class CatalogAttributeCategoryController extends AbstractController{
    static function mass_update(Request $request){
        $items = $request->get("items");
        $new_items = [];
        foreach($items as $item){
            $catalog_attribute_category = \Catalog\Model\CatalogAttributeCategory::find($item["id"]);
            $catalog_attribute_category->update(["position" => $item["position"], "hidden" => $item["hidden"]]);
            $new_items[] = $catalog_attribute_category;
        }
        return response($new_items);
    }
}