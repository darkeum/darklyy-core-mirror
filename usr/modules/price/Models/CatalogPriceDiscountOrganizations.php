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
use Catalog\Models\Catalog;
use Price\Models\CatalogPriceDiscountGroups;

class CatalogPriceDiscountOrganizations extends Model
{
    protected $fillable = ["oid", "cid", "type", "auto"];

    static public function action($request)
    {
        $action = ["action" => false, "action_rus" => false,];
        $data = static::where("oid", $request["oid"])->where("cid", $request["cid"])->get();
        if (empty($request["type"])) {
            $action["action"] = "delete";
            $action["action_rus"] = "удалено";
            foreach ($data as $item) {
                $item->delete();
            }
        } else {
            if (count($data) > 0) {
                $action["action"] = "update";
                $action["action_rus"] = "обновлено";
                $data[0]->update($request);
            } else {
                $action["action"] = "add";
                $action["action_rus"] = "добавлено";
                static::create($request);
            }
        }
        return $action;
    }

    static public function change_groups($cid)
    {
        $c = Catalog::findOrFail($cid);
        $data = static::where("cid", $cid)->where("auto", 1)->get();
        foreach ($data as $item) {
            $dc = CatalogPriceDiscountGroups::withTrashed()->where("oid", $item->oid)->where("catalog_groups_id", $c->catalog_groups_id)->first();
            if (!empty($dc)) {
                $item->type = $dc->type;
                $item->save();
            }
        }
    }
}
