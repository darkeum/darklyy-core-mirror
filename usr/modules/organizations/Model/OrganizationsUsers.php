<?php

namespace Organizations\Model;

class OrganizationsUsers extends \Model\Model{
    protected $fillable = ["oid", "uid", "type"];

    public function organization(){
        return $this->belongsTo(\Organizations\Model\Organizations::class, "oid");
    }

    public function user(){
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }
}