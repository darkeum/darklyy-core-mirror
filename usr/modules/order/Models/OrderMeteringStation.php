<?php

namespace Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMeteringStation extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["order_id", "metering_data", "status"];
  
    protected $casts = [
        'metering_data' => "array", //array, datetime, date
    ];

    public function request()
    {
        return $this->belongsTo(\Request\Model\Request::class, "request_id");
    }

}