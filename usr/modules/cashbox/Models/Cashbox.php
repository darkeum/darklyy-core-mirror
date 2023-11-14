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

class Cashbox extends Model
{
    protected $fillable = ["title", "address", "coordinates", "NumDevice", "KktNumber", "org"];

    public function cashiers()
    {
        return $this->hasMany(Cashiers::class);
    }
}
