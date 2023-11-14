<?php

namespace Catalog\Controller;

use \Boot\System\Http\Request;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;
use \Catalog\Grid\IndexGrid;

class CatalogAttributeController extends AbstractController{

    static function adminUsers(){
        return [1, 33];
    }

    static function index(){
        display("@catalog/manager/attribute/index", [
            "categories" => \Boot\Support\Facades\Categories::getFullTree('catalog_v2'),
            "attributes" => \Catalog\Model\CatalogAttribute::get_cache_all(),
            "types" => \Catalog\Model\CatalogAttribute::getType(),
            "adminUsers" => static::adminUsers(),
        ]);
    }

    static function search(Request $request){
        $value = $request->q;
        return response(\Catalog\Model\CatalogAttribute::orWhere("name", "LIKE", "%{$value}%")->orWhere("id", $value)->get());
    }

    static function get_values($id){
        return response(\Catalog\Model\CatalogAttribute::with(["attribute_value" => function($query){
            $query->orderBy("value")->orderBy("id");
        }])->where("id", $id)->first());
    }

    static function store(Request $request){
        $attribute = \Catalog\Model\CatalogAttribute::create($request->all());
        $categories = $request->category ?? [];
        $catalog_attribute_category = [];
        foreach($categories as $category){
            $catalog_attribute_category[] = \Catalog\Model\CatalogAttributeCategory::create([
                "attribute_id" => $attribute->id,
                "category_id" => $category,
            ]);
        }
        return response(["attribute" => $attribute, "catalog_attribute_category" => $catalog_attribute_category]);
    }

    static function load($category_id){
        return response(\Catalog\Model\CatalogAttribute::with(["attribute_category_one" => function($query) use ($category_id){
            $query->where("category_id", $category_id);
        }])->whereHas("attribute_category", function($query) use ($category_id){
            $query->where("category_id", $category_id)->orderBy("position")->orderBy("id");
        })->get());
    }

    static function load_all(){
        return response(\Catalog\Model\CatalogAttribute::all());
    }

    static function get($id){
        $data = \Catalog\Model\CatalogAttribute::with(["attribute_category", "attribute_value" => function($query){
            $query->orderBy("position");
        }])->where("id", $id)->first();
        return response(["data" => $data, "categories" => \Boot\Support\Facades\Categories::getFullTree('catalog_v2')]);
    }

    static function update(Request $request, $id){
        $attribute = \Catalog\Model\CatalogAttribute::findOrFail($id);
        $old_type = $attribute->type;
        $attribute->update($request->all());
        
        $catalog_attribute_category = [];
        $categories = $request->category ?? [];
        $query = \Catalog\Model\CatalogAttributeCategory::where("attribute_id", $attribute->id);
        if(static::wasChangeCategory($query->get(), $categories)){
            $query->delete();
            foreach($categories as $category_id){
                $catalog_attribute_category[] = \Catalog\Model\CatalogAttributeCategory::create(["attribute_id" => $attribute->id, "category_id" => $category_id]);
            }
        }

        if($attribute->wasChanged("type")){
            \Catalog\Jobs\CatalogAttributeChangeTypeJob::dispatch($attribute, $old_type, $attribute->type);
        }

        return response(["attribute" => $attribute, "catalog_attribute_category" => $catalog_attribute_category]);
    }

    static function wasChangeCategory($items, $categories){
        if(count($items) != count($categories)){
            return true;
        }
        foreach($items as $item){
            if(!in_array($item->category_id, $categories)){
                return true;
            }
        }

        return false;
    }

    static function destroy($id){
        $catalog_attribute = \Catalog\Model\CatalogAttribute::findOrFail($id);
        $catalog_attribute->delete();
        \Catalog\Jobs\CatalogAttributeDeleteJob::dispatch($catalog_attribute);
        return response($catalog_attribute);
    }

    static function all(){
        $categories = [];
        foreach(\Catalog_v2\Model\Categories::where("module", "catalog_v2")->orderBy("name")->get() as $item){
            $categories[ $item->parent_id ][ $item->id ] = $item;
        }
        $attribute = [];
        foreach(\Catalog\Model\CatalogAttribute::with(["attribute_value" => function($query){
            $query->orderBy("position");
        }])->get() as $item){
            $attribute[ $item->id ] = $item;
        }
        $categories_attribute = [];
        foreach(\Catalog\Model\CatalogAttributeCategory::all() as $item){
            $categories_attribute[ $item->category_id ][] = $item;
        }
        display("@catalog/manager/attribute/all", [
            "categories" => $categories,
            "attribute" => $attribute,
            "categories_attribute" => $categories_attribute,
        ]);
    }
}