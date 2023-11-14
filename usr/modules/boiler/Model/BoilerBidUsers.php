<?php

namespace Boiler\Model;

class BoilerBidUsers extends \Model\Model{
    protected $fillable = ["bid_id", "type", "uid"];

    public function user(){
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }//
}