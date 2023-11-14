<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

use Boot\Support\Facades\Auth;

class ExtendedSortTemplate extends Model
{
    protected $fillable = ["uid", "name", "common", "template"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }
}
