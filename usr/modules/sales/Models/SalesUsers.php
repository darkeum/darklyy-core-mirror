<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sales\Models;

use Boot\Models\Model;
use Boot\Models\Users;
use Sales\Models\Sales;

class SalesUsers extends Model
{
    protected $fillable = ["sales_id", "type", "uid"];

    protected $appends = ["type_view"];

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "uid");
    }

    static function getType()
    {
        return [
            "CLIENT" => "Клиент",
        ];
    }

    public function getTypeViewAttribute()
    {
        $type = static::getType();
        return $type[$this->type] ?? $this->type;
    }
}
