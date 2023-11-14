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

use \Illuminate\Database\Eloquent\SoftDeletes;

class UsersSign extends Model
{
    use SoftDeletes;

    protected $fillable = ["uid", "type", "base", "base_numb", "base_date", "sign", "date", "date_start", "date_end", "org", "type_post", "status"];

    protected $attributes = [
        'sign' => 1,
    ];

    static function getType()
    {
        return [
            1 => "Основное ответственное лицо организации",
            2 => "Лицо, имеющее право подписи документов",
        ];
    }

    public function getTypeViewAttribute()
    {
        return static::getType()[$this->type] ?? $this->type;
    }

    static function getBase()
    {
        return [
            1 => "Приказ",
            2 => "Доверенность",
        ];
    }

    public function getBaseViewAttribute()
    {
        return static::getBase()[$this->base] ?? $this->base;
    }

    static function getTypePost()
    {
        return [
            1 => "Руководитель",
            2 => "Главный бухгалтер",
            3 => "Кассир",
        ];
    }

    public function getTypePostViewAttribute()
    {
        return static::getTypePost()[$this->type_post] ?? $this->type_post;
    }
}
