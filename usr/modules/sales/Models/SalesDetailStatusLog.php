<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sales\Models;

use Boot\Models\Model;
use Boot\Support\Facades\Auth;

class SalesDetailStatusLog extends Model
{
    protected $fillable = ["sales_detail_id", "uid", "status", "url"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }
}
