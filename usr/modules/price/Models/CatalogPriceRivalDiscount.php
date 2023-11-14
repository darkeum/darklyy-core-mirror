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

class CatalogPriceRivalDiscount extends Model
{

    protected $fillable = ["cid", "discount", "host"];

    static function action($request)
    {
        $item = static::where("cid", $request["cid"])->where("host", $request["host"])->first();
        if ($item) {
            $item->update(["discount" => $request["discount"]]);
        } else {
            $item = static::create($request);
        }
        return $item;
    }
}
