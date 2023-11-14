<?php

namespace Catalog_v2\Model;

class CertRelation extends \Model\Model{
    protected $fillable = ["cid", "cert_id", "hash", "date", "status"];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->hash = empty($model->hash) ? "{$model->cid}_{$model->cert_id}" : $model->hash;
            $model->date = empty($model->date) ? date("Y-m-d") : $model->date;
            $model->status = empty($model->status) ? 1 : $model->status;
        });
    }
}