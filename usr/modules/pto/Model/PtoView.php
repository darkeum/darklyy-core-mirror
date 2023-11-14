<?php

namespace Pto\Model;

use \Boot\Support\Facades\Auth;

class PtoView extends \Model\Model{
    protected $fillable = ["pto_id", "type", "numb", "p", "l", "l1", "l2", "hairpin_type", "hairpin_count", "plates_min", "plates_max", "weight", "dn", "mesh_d", "bolt_count", "bolt_type", "cad_2d", "cad_3d", "cad_revit", "date", "uid", "status"];

    public static function boot(){
        parent::boot();
        
        static::creating(function($model){
		    $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }

    static public function getType(){
        return [1 => "общепромышленное / специальное исполнение", "общепромышленное исполнение", "специальное исполнение"];
    }

    public function getTypeViewAttribute(){
        return static::getType()[ $this->type ] ?? $this->type;
    }
}