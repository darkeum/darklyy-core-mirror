<?php

namespace Merging\Model;

use \Boot\Traits\SoftDeletesDel;
use \Boot\Support\Facades\Auth;

class Merging extends \Model\Model{
    use SoftDeletesDel;

    protected $fillable = ["numb_counter", "numb", "uid", "object", "date", "cid", "name", "sku", "unit", "count", "stock_id", "org", "type", "active", "transfer_list"];

    protected $casts = [
        'transfer_list' => "array",
    ];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date = \Carbon\Carbon::now();
            $numb = \Boot\Support\Module::numb('merging', $model->org, $model->date);
            $model->numb = $numb["numb"];
            $model->numb_counter = $numb["numb_counter"];
        });
    }

    public function merging_detail(){
        return $this->hasMany(\Merging\Model\MergingDetail::class);
    }

    public function object_(){
        return $this->belongsTo(\Object\Model\ObjectModel::class, "object");
    }

    public function stock(){
        return $this->belongsTo(\Stocks\Models\Stock::class);
    }

    public function org_(){
        return $this->belongsTo(\Organizations\Model\Org::class, "org");
    }
}