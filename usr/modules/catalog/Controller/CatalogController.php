<?php

namespace Catalog\Controller;

use \Boot\System\Http\Request;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;
use \Catalog\Grid\IndexGrid;

class CatalogController extends AbstractController{
    static function index(){
        $grid = new IndexGrid;
        display("@catalog/manager/index.html.twig", [
            'grid' =>  $grid->render(),
        ]);
    }

    static function createOrEditData($options = []){
        $catalog = $options["catalog"] ?? false;
        $catalog_groups_select = [];
        $oSelectCustomData = [
            "mod" => "jsReturn", 
            "name" => "catalog_groups_id", 
            "id" => $catalog ? $catalog->catalog_groups_id : null,
            "ajaxUrl" => "getCatalogGroupsToSelect",
            "allowClear" => true,
            "bootstrap" => true,
            "return" => true,
        ];
        $catalog_groups_select["js"] = oSelectCustom($oSelectCustomData);
        $oSelectCustomData["mod"] = "select";
        $catalog_groups_select["select"] = oSelectCustom($oSelectCustomData);
        
        return [
            "categories" => \Boot\Support\Facades\Categories::getFullTree('catalog_v2'),
            "catalog_groups_select" => $catalog_groups_select,
            "catalog_sections" => \Catalog_v2\Model\CatalogSection::get_cache_all(),
            "units" => \Catalog_v2\Model\CatalogUnit::get_cache_all(),
            "manufactures" => \Catalog_v2\Model\CatalogManufacture::get_cache_all(),
            "types" => \Catalog_v2\Model\CatalogTypes::get_cache_all(),
        ];
    }

    static function getAttributes($catalog){
        $attributes = [];
        if($catalog && !empty($catalog->category)){
            $attributes = \Catalog\Model\CatalogAttribute::whereHas("attribute_category", function($query) use ($catalog){
                $query->where("category_id", str_replace(",", "", $catalog->category));
            })->get();
            foreach($attributes as $attribute){
                if(!empty($catalog->id)){
                    $attribute->relation = \Catalog\Model\CatalogAttributeRelation::where("cid", $catalog->id)->where("attribute_id", $attribute->id)->first();
                }
                if($attribute->type == "selection"){
                    $attribute->values = \Catalog\Model\CatalogAttributeValue::where("attribute_id", $attribute->id)->get();
                }
            }
        }
        return [
            "attributes" => $attributes,
        ];
    }

    static function create(){
        $catalog = new \Catalog_v2\Model\Catalog();
        $catalog->types = 3;
        display("@catalog/manager/createOrEdit", [
            "catalog" => $catalog,
        ] + static::createOrEditData());
    }

    static function store(Request $request){
        $catalog = \Catalog_v2\Model\Catalog::create($request->all());
        static::createOrUpdateFunctions($catalog, $request);
        return redirect(router('admin.catalog.edit', $catalog->id))->with('status', 'Товар успешно создан!'); 
    }

    static function save_attribute($catalog, Request $request){
        if($catalog->wasChanged("category")){
            \Catalog\Model\CatalogAttributeRelation::where("cid", $catalog->id)->delete();
        }
        if($request->has("attribute_data")){
            $attribute_data = $request->get("attribute_data");
            foreach($attribute_data as $attribute_type => $attribute_values){
                foreach($attribute_values as $attribute_id => $attribute_value){
                    $catalog_attribute_relation = \Catalog\Model\CatalogAttributeRelation::firstOrNew(["attribute_id" => $attribute_id, "cid" => $catalog->id]);
                    if(empty($attribute_value)){
                        if($catalog_attribute_relation->exists){
                            $catalog_attribute_relation->delete();
                        }
                    } else {
                        $catalog_attribute_relation->value_text = null;
                        $catalog_attribute_relation->attribute_value_id = null;
                        switch($attribute_type){
                            case "selection":
                                $catalog_attribute_relation->attribute_value_id = $attribute_value;
                            break;
                            case "manual":
                                $catalog_attribute_relation->value_text = $attribute_value;
                            break;
                        }
                        $catalog_attribute_relation->save();
                    }
                }
            }
        }
    }

    static function save_preview($catalog, Request $request){
        if ($request->has("preview_del")){
			if (file_exists(ROOT.$catalog->preview)) unlink(ROOT.$catalog->preview);
			$catalog->preview = '';
			$catalog->save();
		} else {
            if($request->hasFile("preview")){
                $file = $request->file("preview");
                $purl = 'files/catalog/'.$catalog->id.'/';
				$path_info = pathinfo($file->getClientOriginalName());
				$ext = $path_info['extension'];
                $new_file_name = 'preview_' .$catalog->id.'.'.$ext;
				$file_full = $purl.$new_file_name;
				if (file_exists(ROOT.$file_full)) {
					@unlink(ROOT.$file_full);
				}
                $file->move(ROOT.$purl, $new_file_name);
                $catalog->preview = "/{$file_full}";
				$catalog->save();
            }
		}
    }

    static function save_attach_files($catalog, Request $request){
        if($request->hasFile("attach_id")){
			$aid_file = $request->file('attach_id');
			$attach = new \Boot\Models\Attach;
			$attach->name = $aid_file->getClientOriginalName();
			$new_file_name = time()."_".$aid_file->getClientOriginalName();
			$patch = "files/calc/".$new_file_name;
			$attach->url = $patch;
			$attach->pid = $catalog->id;
			$attach->module = "catalog_v2";
			$attach->save();

            $aid_file->move(ROOT.$patch, $new_file_name);

			$orig = $catalog->attach_id;
			$orig[] = $attach->id;
			$catalog->attach_id = $orig;
			$catalog->save();
		}
    }

    // static function show($id, Request $request){
        
    // }

    static function edit($id){
        $catalog = \Catalog_v2\Model\Catalog::findOrFail($id);
        $files = [];
        if(!empty($catalog->attach_id)){
            $files = \Boot\Models\Attach::find($catalog->attach_id);
        }
        display("@catalog/manager/createOrEdit", [
            "catalog" => $catalog,
            "files" => $files,
        ] + static::createOrEditData([
            "catalog" => $catalog,
        ]));
    }

    static function update(Request $request, $id){
        $catalog = \Catalog_v2\Model\Catalog::find($id);
        $catalog->update($request->all());
        static::createOrUpdateFunctions($catalog, $request);
        return redirect(router('admin.catalog.edit', $id))->with('status', 'Товар успешно обновлён!'); 
    }

    static function createOrUpdateFunctions(\Catalog_v2\Model\Catalog $catalog, Request $request){
        static::save_attribute($catalog, $request);
        static::save_preview($catalog, $request);
        static::save_attach_files($catalog, $request);
        \Catalog\Jobs\CatalogAttributeJob::dispatch($catalog);
    }

    // static function destroy($id){
        
    // }

    static function get_attributes(Request $request){
        $catalog = new \Catalog_v2\Model\Catalog($request->all());
        $catalog->id = $request->id ?? null;
        display("@catalog/manager/attribute", [
            "catalog" => $catalog,
            "attribute_type" => \Catalog\Attribute\Attribute::getTypes(),
        ] + static::getAttributes($catalog));
    }

    static function sku_generate(Request $request){
        return response(["value" => \Catalog_v2\Model\Catalog::$NEW_SKU]);
    }


}