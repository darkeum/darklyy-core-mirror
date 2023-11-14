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

class UsersPhoneid extends Model
{
    protected $fillable = ["uid", "phoneid", "status"];

    public function user()
    {
        return $this->hasOne(Users::class, "id", "uid");
    }
}
