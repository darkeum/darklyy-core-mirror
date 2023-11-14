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
use Boot\Traits\SoftDeletesDel;

class StorageAssembly extends Model
{
    use SoftDeletesDel;

    protected $fillable = ["del", "sales_id", "uid", "uid_end", "date", "type", "status"];

    protected $attributes = [
        'status' => 1,
    ];

    public function storage_assembly_detail()
    {
        return $this->hasMany(\Storage\Models\StorageAssemblyDetail::class, "said");
    }

    public function sales()
    {
        return $this->belongsTo(\Sales\Models\Sales::class, "sales_id");
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->date = empty($model->date) ? \Carbon\Carbon::now() : $model->date;
            $model->uid = empty($model->uid) ? \Boot\Support\Facades\Auth::id() : $model->uid;
        });
    }
}
