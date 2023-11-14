<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Storage\Models;

use Boot\Models\Model;
use Boot\Traits\SoftDeletesDel;

class StockTransfer extends Model{
    use SoftDeletesDel;
    
    protected $fillable = ["del", "numb_counter", "numb", "title", "uid", "uid_gave", "uid_accepted", "object", "org", "sales_id", "stock_out", "stock_in", "comment", "date", "date_active", "scan", "has_null", "active", "status"];

    public static function boot(){
        parent::boot();
        
        static::creating(function($model){
            $model->date = empty($model->date) ? \Carbon\Carbon::now() : $model->date;
            $numb_array = \Boot\Support\Module::numb('stock_transfer', $model->org, $model->date);
	        $model->numb = $numb_array['numb'];
	        $model->numb_counter = $numb_array['numb_counter'];

            $model->uid = \Boot\Support\Facades\Auth::id();

            if(empty($model->uid_gave)) $model->uid_gave = $model->uid;
            if(empty($model->uid_accepted)) $model->uid_accepted = $model->uid;
        });

        static::created(function($model){
            setLog("info", $model->id, "stock_transfer", 'Создание перемещения №'.$model->numb.'; организация - '.$model->org.'; Склад отправитель - '.$model->stock_out.', склад получатель  - '.$model->stock_in.'; объект - '.$model->object);	
            
            if(!empty($model->object)) relationsSave('object', $model->object, 'transfer', $model->id);	
        });

    }

    public function stock_transfer_detail(){
        return $this->hasMany(\Storage\Models\StockTransferDetail::class, "transfer_id");
    }
}