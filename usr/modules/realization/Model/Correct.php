<?php

namespace Realization\Model;

class Correct extends \Model\Model{
    protected $fillable = ["numb_counter", "numb", "org", "pid", "module", "realization_id", "uid", "date_create", "date", "active"];

    public function correct_detail(){
        return $this->hasMany(\Realization\Model\CorrectDetail::class);
    }

    public function realization(){
        return $this->belongsTo(\Realization\Model\Realization::class, "realization_id");
    }

    static function checkCorrectMod($id){
        $correct = static::findOrFail($id);
        return !empty($correct->module) && $correct->module == "correct";
    }
}