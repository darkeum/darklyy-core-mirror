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
use Boot\Support\Facades\Auth;
use Storage\Models\StorageDetail;
use Illuminate\Database\Eloquent\SoftDeletes;

class Storage extends Model
{
    use SoftDeletes;

    protected $fillable = ["uid", "oid", "object", "stock_id", "type", "sales_id", "supply_id", "sa_id", "date", "date_end", "comment", "send_notif_uid", "status"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }

    public function storage_detail()
    {
        return $this->hasMany(StorageDetail::class);
    }
}
