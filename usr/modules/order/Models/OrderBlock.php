<?php

namespace Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBlock extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["order_id", "block_data", "status"];
  
    protected $casts = [
        'block_data' => "array", //array, datetime, date
    ];

    public function request()
    {
        return $this->belongsTo(\Request\Model\Request::class, "request_id");
    }


}