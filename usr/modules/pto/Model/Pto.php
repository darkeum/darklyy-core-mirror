<?php

namespace Pto\Model;

use \Boot\Support\Facades\Auth;

class Pto extends \Model\Model{
    protected $fillable = ["title", "altname", "dn", "sp", "st", "img_2d", "img_3d", "man_id", "t_from", "t_to", "q_max", "p_max", "gasket_type", "plates_type", "coolant", "main_plates", "other_plates", "warranty", "ptoTags",  "uid", "status"];

    protected $casts = [
        'gasket_type' => "array",
        'plates_type' => "array",
        'coolant' => "array",
        'main_plates' => "array",
        'other_plates' => "array",
        'ptoTags' => "array",
    ];

    public static function boot(){
        parent::boot();
        
        static::creating(function($model){
		    $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }
    
    static public function getGasketType(){
        return [1 => "Nitrile", "EPDM", "Silicone", "Viton и их модификации"];
    }

    public function getGasketTypeViewAttribute(){
        return static::getGasketType()[ $this->gasket_type ] ?? $this->gasket_type;
    }

    static public function getPlatesType(){
        return [1 => "AISI 304", "AISI 316", "SMO 254", "Titan", "Hastelloy C-276"];
    }

    public function getPlatesTypeViewAttribute(){
        return static::getPlatesType()[ $this->plates_type ] ?? $this->plates_type;
    }

    static public function getCoolant(){
        return [1 => "вода", "пар", "этиленгликоль", "масла", "нефть", "кислоты", "пищевые жидкости", "фреон", "другое"];
    }

    public function getCoolantViewAttribute(){
        return static::getCoolant()[ $this->coolant ] ?? $this->coolant;
    }

    static public function getMainPlates(){
        return [1 => "TK", "TL", "TM"];
    }

    public function getMainPlatesViewAttribute(){
        return static::getMainPlates()[ $this->main_plates ] ?? $this->main_plates;
    }

    static public function getOtherPlates(){
        return [1 => "ТМTL", "ТКТМ", "ТКТL", "TLX", "TLTLX", "TYK"];
    }

    public function getOtherPlatesViewAttribute(){
        return static::getOtherPlates()[ $this->other_plates ] ?? $this->other_plates;
    }
}