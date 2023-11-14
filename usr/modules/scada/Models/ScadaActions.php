<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Models;

use Boot\Traits\Key;
use Boot\Models\Model;
use Boot\System\Database\Eloquent\SoftDeletes;

class ScadaActions extends Model
{
    use SoftDeletes, Key;

    protected $fillable = ["tag_id", "value", "uid", "status"];

    static $key = ROOT . '/tmp/key/scada.daemon.key';

    public static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            static::key_create();
        });
    }

    public function tags()
    {
        return $this->belongsTo(ScadaTags::class, "tag_id");
    }
}
