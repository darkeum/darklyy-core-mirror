<?php

namespace Project\Model;

use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Blocks extends \Boot\Models\Model{
    use SoftDeletes;

    protected $connection = 'AEWIN';

    protected $fillable = ["uid", "type", "code", "3d", "2d_back", "2d_left", "2d_all", "2d_plan", "2d_right", "2d_front", "2d_back_preview", "2d_left_preview", "2d_all_preview", "2d_plan_preview", "2d_right_preview", "2d_front_preview"];

    public static function boot(){
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = Auth::id();
            }
        });
    }

    static function getType(){
        return [
            "bov" => "БОВ", 
            "bgv" => "БГВ", 
            "bp" => "БП", 
            "bv" => "БВ",
        ];
    }

    public function getTypeViewAttribute(){
        return static::getType()[ $this->type ] ?? $this->type;
    }

    static function get2dList(){
        return ["2d_back", "2d_left", "2d_all", "2d_plan", "2d_right", "2d_front"];
    }

    static function getView(){
        return [
            "3d" => "3d",
            "2d_back" => "2d зад",
            "2d_left" => "2d лев",
            "2d_all" => "2d общ",
            "2d_plan" => "2d план",
            "2d_right" => "2d прав",
            "2d_front" => "2d проф",
        ];
    }

    static function getViewLinks(){
        return [
            "3d" => ["3d"],
            "2d_back" => ["зад"],
            "2d_left" => ["лев"],
            "2d_all" => ["общ"],
            "2d_plan" => ["план"],
            "2d_right" => ["прав"],
            "2d_front" => ["проф"],
        ];
    }

    static function findView($view){
        $links = static::getViewLinks();
        foreach($links as $key => $link){
            if(array_search($view, $link) !== false){
                return $key;
            }
        }
        return false;
    }

    static function parseFilename($filename){
        $filename_ = $filename;
        $filename_ = trim($filename_);
        $filename_ = preg_replace('/\s+/', '', $filename_);

        //search type
        $type = null;
        foreach(static::getType() as $type_ => $type_rus){
            if(preg_match('/('.$type_rus.')[0-9].*/', $filename_)){
                $type = $type_;
                $filename_ = str_replace($type_rus, "", $filename_);
                break;
            }
        }
        //search view
        foreach(static::getViewLinks() as $view => $views_rus){
            foreach($views_rus as $view_rus){
                if(strpos($filename_, $view_rus) !== false){
                    return [
                        "type" => $type,
                        "view" => $view,
                        "code" => str_replace("-{$view_rus}", "", $filename_),
                    ];
                }
            }
        }
        return [
            "type" => $type,
            "view" => "3d",
            "code" => $filename_,
        ];
    }

    public function user(){
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }
}