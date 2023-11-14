<?php

namespace Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderValve extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["order_id", "nagr", "t1", "t2", "q", "p1", "p2", "tmax", "pressure_drop", "status"];
  
    // protected $casts = [
    //     'fill' => "type", //array, datetime, date
    // ];

    public function request()
    {
        return $this->belongsTo(\Request\Model\Request::class, "request_id");
    }


}