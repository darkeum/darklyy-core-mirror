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

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccounts extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'provider_name', 'provider_id'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
