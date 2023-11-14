<?php

namespace Chat\Model;

use \Boot\Support\Facades\Auth;

class Chat extends \Boot\System\Database\Model\Model{
    protected $fillable = ["module", "pid", "uid", "title", "date_create", "type", "last_message_id", "last_message_date"];

    protected $casts = [
        'uid' => "array",
    ];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->date_create = empty($model->date_create) ? \Carbon\Carbon::now() : $model->date_create;
            $model->type = empty($model->type) ? 1 : $model->type;
        });
    }

    public function chat_user(){
        return $this->hasMany(\Chat\Model\ChatUser::class);
    }

    public function chat_message(){
        return $this->hasMany(\Chat\Model\ChatMessage::class);
    }

    public function last_message(){
        return $this->belongsTo(\Chat\Model\ChatMessage::class, "last_message_id");
    }
}