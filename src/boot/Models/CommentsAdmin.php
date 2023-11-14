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

class CommentsAdmin extends Model
{
    protected $fillable = ["uid", "text", "date", "module", "module_id"];

    protected $appends = ['date_view'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date = empty($model->date) ? Carbon::now() : $model->date;
        });
    }

    public function user()
    {
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }

    public function attach()
    {
        return $this->hasMany(\Boot\Models\Attach::class, "pid");
    }

    public function getDateViewAttribute()
    {
        return date("d.m.Y H:i:s", strtotime($this->date));
    }
}
