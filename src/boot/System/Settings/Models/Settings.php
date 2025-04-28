<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings\Models;

use Boot\Models\Model;

class Settings extends Model
{
    protected $fillable = ['uid', 'name', 'value', 'type'];

    public function user()
    {
        return $this->belongsTo('Boot\Models\User', 'id');
    }
}
