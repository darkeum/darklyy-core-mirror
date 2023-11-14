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

class ElectronicConfirmation extends Model
{
    protected $fillable = ["module", "pid", "uid", "used", "active", "code"];

    static $code_length = 6;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->code = empty($model->code) ? static::generate_code() : $model->code;
        });
    }

    static function generate_code()
    {
        $code = [];
        for ($i = 0; $i < static::$code_length; $i++) {
            $code[] = rand(0, 9);
        }
        return implode("", $code);
    }
}
