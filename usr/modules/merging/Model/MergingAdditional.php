<?php

namespace Merging\Model;

use \Boot\Support\Facades\Auth;

class MergingAdditional extends \Model\Model{

    protected $fillable = ["merging_id", "date", "uid", "active"];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date = \Carbon\Carbon::now();
        });
    }

    public function merging(){
        return $this->belongsTo(\Merging\Model\Merging::class);
    }

    public function merging_detail_additional(){
        return $this->hasMany(\Merging\Model\MergingDetailAdditional::class);
    }
}