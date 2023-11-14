<?php

namespace Merging\Model;

class MergingDetail extends \Model\Model{
    protected $fillable = ["merging_id", "star", "cid", "name", "unit", "sku", "count", "price"];

    protected $module;
    protected $pid;

    public function merging(){
        return $this->belongsTo(\Merging\Model\Merging::class);
    }

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            if(empty($model->price)){
                switch($model->module){
                    default:
                        $data = \Price::getPrice($model->cid);
                        $model->price = $data->price_purchase_;
                    break;
                    case "sales_detail":
                        $data = \Sales\Model\SalesDetail::getPurchase($model->pid);
                        if(!empty($data->price)){
                            $model->price = $data->price;
                        }
                    break;
                }
            }
            
        });
    }

    public function set_pid($pid){
        $this->pid = $pid;
    }
    public function get_pid(){
        return $this->pid;
    }
    public function set_module($module){
        $this->module = $module;
    }
    public function get_module(){
        return $this->module;
    }
}