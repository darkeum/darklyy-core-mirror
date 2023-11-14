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

use Boot\Models\Model;
use Storage\Models\StorageAssembly;

class StorageAssemblyDetail extends Model
{
    protected $fillable = ["said", "sdid", "count", "count_save", "count_now", "status"];

    public function storage_assembly()
    {
        return $this->belongsTo(StorageAssembly::class, "said");
    }

    public function sales_detail()
    {
        return $this->belongsTo(\Sales\Models\SalesDetail::class, "sdid");
    }
}
