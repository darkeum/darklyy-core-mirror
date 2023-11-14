<?php

namespace Chat\Model;

use \Boot\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends \Boot\System\Database\Model\Model{
    use SoftDeletes;
    
    protected $fillable = ["chat_id", "uid", "message", "backplate", "date", "file", "active", "reply"];

    protected $appends = ['avatar'];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date = empty($model->date) ? \Carbon\Carbon::now() : $model->date;
        });

        static::created(function($model){
            \Chat\Model\Chat::findOrFail($model->chat_id)->update([
                "last_message_id" => $model->id, 
                "last_message_date" => $model->date,
            ]);
        });
    }

    public function user(){
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }

    public function getAvatarAttribute(){
        return "/".\Boot\Models\Users::avatar($this->uid);
    }
}