<?php

namespace Requests\Models;

use Boot\Models\Attach;
use Boot\Models\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class PumpStationDrawning extends Model
{   
    use SoftDeletes;

    protected $table = "pump_station_drawing";

    protected $fillable = [
        'cid', 'dn_kol', 'count', 'file_id',
    ];

    public function attach()
    {
        return $this->hasOne(Attach::class, 'id','file_id')
            ->where('module', '=', 'request');
    }

}
