<?php

namespace Boiler\Model;

class BoilerBidData extends \Boot\Models\Model{
    protected $fillable = ["bid_type_id", "category", "uid", "comment", "client_comment", "position", "status"];

    public function categories()
    {
        return $this->belongsTo(\Catalog_v2\Model\Categories::class, "category");
    }
}