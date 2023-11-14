<?php

namespace Realization\Model;

use \Boot\Support\Facades\Auth;

class Realization extends \Model\Model{
    protected $fillable = ["numb_counter", "numb", "id_1c", "ytid", "sales_id", "stock_id", "object", "uid", "uid_create", "oid", "org", "date", "date_active", "total", "nds", "nds_includes", "pay", "relation", "scan", "active", "status", "comment", "supply_order_price"];

    public function sales(){
        return $this->belongsTo(\Sales\Models\Sales::class, "sales_id");
    }

    public function org_(){
        return $this->belongsTo(\Organizations\Model\Org::class, "org");
    }

    public function organization(){
        return $this->belongsTo(\Organizations\Model\Organizations::class, "oid");
    }

    public function realization_detail(){
        return $this->hasMany(\Realization\Model\RealizationDetail::class, "rid");
    }

    public function user(){
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }

    public function user_create(){
        return $this->belongsTo(\Boot\Models\Users::class, "uid_create");
    }

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->uid_create = empty($model->uid_create) ? Auth::id() : $model->uid_create;

            $model->date = empty($model->date) ? \Carbon\Carbon::now() : $model->date;
            $numb_array = \Boot\Support\Module::numb('realization', $model->org, $model->date);
		    $model->numb = $numb_array['numb'];
            $model->numb_counter = $numb_array['numb_counter'];
        });

        static::created(function($model){
            relationsSave('sales', $model->sales_id, 'realization', $model->id);
        });
    }
}