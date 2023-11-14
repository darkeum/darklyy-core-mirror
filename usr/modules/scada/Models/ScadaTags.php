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

class ScadaTags extends Model
{
    use SoftDeletes;

    protected $casts = [
        'polling_at'  => "datetime",
    ];

    protected $fillable = ["scada_id", "title", "unit_id", "actions", "actions_type", "registr", "address", "quantity", "recalculation", "coefficient_a", "coefficient_b",  "type", "endian", "byte",  "invert", "field_type", "field_id", "last_value", "notification_flag", "notifiable", "uid", "polling_at", "status"];

    public function scada()
    {
        return $this->belongsTo(Scada::class, "scada_id");
    }
}
