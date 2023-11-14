<?php

namespace Fcontrol\Model;

class ControlStorage extends \Model\Model{
    protected $fillable = ["title", "date", "status"];

    public function control_storage_detail(){
        return $this->hasMany(\Fcontrol\Model\ControlStorageDetail::class);
    }
}