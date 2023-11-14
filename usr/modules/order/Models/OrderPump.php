<?php

namespace Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPump extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["order_id", "pressure", "qPump", "type", "r1", "r2", "status"];
  
    // protected $casts = [
    //     'fill' => "type", //array, datetime, date
    // ];

    public function request()
    {
        return $this->belongsTo(\Request\Model\Request::class, "request_id");
    }


}