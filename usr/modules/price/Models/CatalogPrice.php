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
use Boot\Models\Attach;
use Boot\Support\Facades\Auth;

class CatalogPrice extends Model
{

    protected $fillable = ["price_type", "uid", "aid", "module", "pid", "date", "comment", "active", "auto", "daemon", "work", "status"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }

    public function detail()
    {
        return $this->hasMany(CatalogPriceDetail::class, "price_id");
    }

    public function type()
    {
        return $this->belongsTo(CatalogPriceType::class, "price_type");
    }

    public function attach()
    {
        return $this->belongsTo(Attach::class, "aid");
    }
}
