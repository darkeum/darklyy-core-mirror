<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Cashbox\Models;

use Boot\Models\Model;
use Boot\Models\Users;

class Cashiers extends Model
{
    protected $fillable = ["cashbox_id", "uid"];

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "uid");
    }

    public function getNameAttribute()
    {
        return $this->user->short_user_name;
    }

    public function getInnAttribute()
    {
        return $this->user->inn;
    }
}
