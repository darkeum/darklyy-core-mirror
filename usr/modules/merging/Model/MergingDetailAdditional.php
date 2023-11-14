<?php

namespace Merging\Model;

class MergingDetailAdditional extends \Model\Model{
    protected $fillable = ["merging_additional_id", "cid", "name", "unit", "sku", "count", "price"];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            if(!isset($model->price)){
                $data = \Price::getPrice($model->cid);
                $model->price = $data->price_purchase_;
            }
        });
    }

    public function merging_additional(){
        return $this->belongsTo(\Merging\Model\MergingAdditional::class);
    }
}