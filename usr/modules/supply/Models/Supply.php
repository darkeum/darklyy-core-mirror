<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Supply\Models;

use Boot\Models\Model;
use Boot\Support\Facades\Auth;

class Supply extends Model
{

    protected $fillable = ["org", "uid", "sales_id", "date", "date_edit", "date_end", "comment", "type", "status"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }
}
