<?php

namespace Catalog_v2\Model;

use \Boot\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Array_;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pump extends \Model\Model{
    use SoftDeletes;

    protected $fillable = ["cid", "uid", "uid_change", "data", "type_of_instalation", "work_line", "type", "max_x", "max_y", "min_x", "min_y", "priority"];

    static $delimiter = ["	", "\n"];

    protected $casts = [
        'data' => "object",
    ];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });

        static::saving(function($model){
            $model->uid_change = empty($model->uid_change) ? Auth::id() : $model->uid_change;
            if(gettype($model->data) == "string" && !empty($model->data)){
                $model->data = static::text_to_data($model->data);
            }
        });
    }

    static function data_to_text(Array $data):String{
        $result = [];
        foreach($data as $val){
            $str = [];
            foreach($val as $v){
                $str[] = $v;
            }
            $result[] = implode(static::$delimiter[0], $str);
        }
        return implode(static::$delimiter[1], $result);
    } 

    static function text_to_data(String $text):Array{
        $result = [];
        $first = explode(static::$delimiter[1], $text);
        foreach($first as $val){
            if(!empty($val)){
                $str = explode(static::$delimiter[0], $val);
                $result[] = [
                    "Q" => trim($str[0]),
                    "H" => trim($str[1]),
                ];
            }
        }
        return $result;
    }

    static public function getTypeOfInstalation(){
        return ["Отсутствует", "Сдвоенный насос Главный-Пиковый", "Сдвоенный насос Главный-Резервный"];
    }

    public function getTypeOfInstalationViewAttribute(){
        return static::getTypeOfInstalation()[ $this->type_of_instalation ] ?? $this->type_of_instalation;
    }

    static public function getWorkLine(){
        return ["Отсутствует", "1", "2", "3"];
    }

    public function getWorkLineViewAttribute(){
        return static::getWorkLine()[ $this->work_line ] ?? $this->work_line;
    }

    public function catalog(){
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, 'cid');
    }
}