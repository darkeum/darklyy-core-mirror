<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Models;

use Boot\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Price\UI\CLI\CatalogPriceDiscountGroupsDaemon;

class CatalogPriceDiscountGroups extends Model
{
    use SoftDeletes;

    protected $fillable = ["oid", "catalog_groups_id", "type", "daemon"];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->daemon = 0;
        });
        static::deleting(function ($model) {
            $model->daemon = 0;
        });
    }

    static function action($request)
    {
        $oid = $request["oid"];
        $type = $request["type"];
        $catalog_groups_id = $request["catalog_groups_id"];
        $query = static::withTrashed()->where("oid", $oid)->whereIn("catalog_groups_id", $catalog_groups_id)->get();
        if (empty($type)) {
            foreach ($query as $item) {
                $item->delete();
            }
        } else {
            $s_catalog_groups_id = $catalog_groups_id;
            foreach ($query as $item) {
                $item->update(['type' => $type]);
                $item->restore();
                $key = array_search($item["catalog_groups_id"], $s_catalog_groups_id);
                unset($s_catalog_groups_id[$key]);
            }
            if (count($s_catalog_groups_id) > 0) {
                foreach ($s_catalog_groups_id as $id) {
                    $new_item = [
                        "oid" => $oid,
                        "catalog_groups_id" => $id,
                        "type" => $type,
                    ];
                    static::create($new_item);
                }
            }
        }
        CatalogPriceDiscountGroupsDaemon::key_create();
    }
}
