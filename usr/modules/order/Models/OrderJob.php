<?php

namespace Order\Models;

class OrderJob extends \Boot\Models\Model{
    protected $fillable = ["order_id", "category", "position", "summary", "summary_url", "other_file", "status"];
   
    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    }

    public function request()
    {
        return $this->belongsTo(\Request\Model\Request::class, "request_id");
    }

}