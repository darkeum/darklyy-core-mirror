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

class CatalogPriceDiscount extends Model
{
    protected $fillable = ["cid", "type", "discount"];

    static function action($request)
    {
        return static::updateOrCreate(["cid" => $request["cid"], "type" => $request["type"]], $request);
    }
}
