<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Orgs\Models;

use Boot\Models\Model;
use Boot\Models\Users;
use Boot\Support\Cache;
use Stocks\Models\Stock;
use Organizations\Model\Organizations;

class Org extends Model
{
    protected $fillable = ["uid", "oid", "nds", "nds_includes", "prefix", "taxation", "logo", "bill_sign", "accountant", "director", "phone", "stock_id_default", "change_alert", "status"];

    protected $attributes = [
        'change_alert' => 0,
    ];

    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            static::cache_delete($model->id);
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organizations::class, "oid");
    }

    public function director_user()
    {
        return $this->belongsTo(Users::class, "director");
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, "org");
    }

    static function cache_delete($id)
    {
        Cache::delete("org_info_{$id}");
    }
}
