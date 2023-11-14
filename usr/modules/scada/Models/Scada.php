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

use Boot\Models\Model;
use Boot\System\Database\Eloquent\SoftDeletes;

class Scada extends Model
{
    use SoftDeletes;

    protected $casts = [
        'polling_at'  => "datetime",
    ];

    protected $fillable = ["title", "address", "uid", "oid", "object", "access", "ip", "port",  "rtuConverter",  "date",  "polling_at", "notification_flag", "connection", "notifiable", "status"];

    public function client()
    {
        return $this->hasMany(\Scada\Models\ScadaUsers::class);
    }

    public function organizations()
    {
        return $this->belongsTo(\Organizations\Model\Organizations::class, 'oid');
    }
}
