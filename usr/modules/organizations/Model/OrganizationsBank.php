<?php

namespace Organizations\Model;

class OrganizationsBank extends \Model\Model{
    protected $fillable = ["oid", "name_short", "name", "address", "bik", "kschet", "rschet", "status"];

    public function organization(){
        return $this->belongsTo(\Organizations\Model\Organizations::class, "oid");
    }
}