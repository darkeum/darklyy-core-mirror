<?php

namespace Fcontrol\Model;

class ControlStorageDetail extends \Model\Model{
    protected $fillable = ["control_storage_id", "storage_id", "storage_detail_id", "status"];

    public function control_storage(){
        return $this->belongsTo(\Fcontrol\Model\ControlStorage::class);
    }

    public function storage_detail(){
        return $this->belongsTo(\Storage\Model\StorageDetail::class);
    }
}