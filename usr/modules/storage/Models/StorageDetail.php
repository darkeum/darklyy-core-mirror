<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Storage\Models;

use Storage\Models\Storage;

class StorageDetail extends \Model\Model
{
    protected $fillable = ["cid", "storage_id", "sale_detail_id", "supply_detail_id", "sad_id", "count", "count_now", "count_stable", "comment", "status", "position"];

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function catalog()
    {
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, "cid");
    }
}
