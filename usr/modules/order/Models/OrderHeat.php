<?php

namespace Order\Models;

class OrderHeat extends \Boot\Models\Model{
    protected $fillable = ["order_id", "request_id", "status"];
    //
    public function request()
    {
        return $this->belongsTo(\Request\Model\Request::class, "request_id");
    }

    public function order()
    {
        return $this->belongsTo(\Order\Models\Order::class, "order_id");
    }

}