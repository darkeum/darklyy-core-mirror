<?php

namespace Pto\Model;

use \Boot\Support\Facades\Auth;

class PtoPrice extends \Model\Model{
    protected $fillable = ["pto_id", "type_in", "type_out", "power", "t1", "t2", "t3", "t4", "price", "aid_calc", "d1", "d2", "date", "uid", "status"];

    public static function boot(){
        parent::boot();
        
        static::creating(function($model){
		    $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }

    static public function getType(){
        return [1 => "вода", "пар", "этиленгликоль", "масла", "нефть", "кислоты", "пищевые жидкости", "фреон", "другие"];
    }

    public function getTypeInViewAttribute(){
        return static::getType()[ $this->type_in ] ?? $this->type_in;
    }

    public function getTypeOutViewAttribute(){
        return static::getType()[ $this->type_out ] ?? $this->type_out;
    }
}