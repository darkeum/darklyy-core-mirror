<?php

namespace Chat\Model;

use \Boot\Support\Facades\Auth;

class ChatUser extends \Boot\System\Database\Model\Model{
    
    protected $fillable = ["chat_id", "uid"];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }

    public function chat(){
        return $this->belongsTo(\Chat\Model\Chat::class);
    }
}