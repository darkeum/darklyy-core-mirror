<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Golovin Egor
*/

namespace Catalog\UI\API\Transformers;

use Catalog\Models\Catalog;
use Boot\Support\Facades\Auth;
use Boot\Abstracts\Transformers\Transformer;

class CatalogTransformer extends Transformer
{
    public function transform(Catalog $item): array
    {
        if (Auth::id() == 1) {
            $response = [
                'object' => $item->getResourceKey(),
                'id' => $item->id,
                "multi_id" => $item->id,
                "sku" => $item->sku, 
                "sku2" => $item->sku2, 
                "sku3" => $item->sku3, 
                "sku_UdmurtSnab" => $item->sku_UdmurtSnab, 
                "unit" => $item->unit, 
                "title" => $item->title, 
                "fulltitle" => $item->fulltitle, 
                "short" => $item->short, 
                "full" => $item->full, 
                "uid" => $item->uid, 
                "tags" => $item->tags, 
                "price_data" => $item->price_data, 
                "category" => $item->category, 
                "attach_id" => $item->attach_id, 
                "altname" => $item->altname, 
                "preview" => 'https://atri-energo.ru'. $item->preview, 
                "keywords" => $item->keywords, 
                "description" => $item->description, 
                "man" => $item->man, 
                "catalog_manufacture" => $item->catalog_manufacture, 
                "allow_rating" => $item->allow_rating, 
                "allow_index" => $item->allow_index, 
                "score" => $item->score, 
                "votes" => $item->votes, 
                "views" => $item->views, 
                "clicks" => $item->clicks, 
                "attribute" => $item->attribute, 
                "attribute_save" => $item->attribute_save, 
                "publish_urls" => $item->publish_urls, 
                "fixed" => $item->fixed, 
                "types" => $item->types, 
                "spec" => $item->spec, 
                "weight" => $item->weight, 
                "date_delivery" => $item->date_delivery, 
                "qmin" => $item->qmin, 
                "qnorm" => $item->qnorm, 
                "hidden" => $item->hidden, 
                "check_update" => $item->check_update, 
                "active" => $item->active, 
                "active_guest" => $item->active_guest, 
                "active_user" => $item->active_user, 
                "catalog_groups_id" => $item->catalog_groups_id, 
                "created_at" => $item->created_at, 
                "updated_at" => $item->updated_at, 
                "url" => 'https://atri-energo.ru/catalog_v2/' . $item->altname . ".html",
            ];
        } else {
            $ndr = get_ndr(["cid" => $item->id, "stock_id" => 1, "module" => "", "pid" => "", "returnType" => "array"]);
            $price = \Price::getPrice($item->id,Array($request["oid"] = 768));
            $response = [
                'id' => $item->id,
                "sku" => $item->sku,
                "unit" => $item->unit,
                "title" => $item->title,
                "short" => $item->short,
                "category" => $item->category,
                "preview" => 'https://atri-energo.ru'. $item->preview,
                "catalog_manufacture" => $item->catalog_manufacture,
                "created_at" => $item->created_at,
                "updated_at" => $item->updated_at,
                "url" => 'https://atri-energo.ru/catalog_v2/' . $item->altname . ".html",
                "price_list_discount_" => $price->price_list_discount_,
                "remainder" => $ndr["d"],
            ];
        }
        return $response;
    }
}