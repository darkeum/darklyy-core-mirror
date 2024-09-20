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

class UsersOrg extends Model
{
    protected $fillable = ["uid", "oid", "post", "post_genitive", "comment", "writ", "type", "owner", "choose", "status"];

    public function user()
    {
        return $this->hasOne(Users::class, "id", "uid");
    }

    static function getType()
    {
        return [
            0 => "Отсутствует",
            1 => "Бухгалтер",
            2 => "Директор",
        ];
    }

    public function getTypeViewAttribute()
    {
        $type = static::getType();
        return $type[$this->type] ?? $this->type;
    }
}
