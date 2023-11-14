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

use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;

class Relations extends Model
{
    protected $fillable = ["module_in", "pid_in", "module_out", "pid_out", "date", "uid", "status"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->date)) $model->date = Carbon::now()->timestamp;
            if (empty($model->uid)) $model->uid = Auth::id();
        });
    }
}
