<?php

namespace General\Model;

class AttachDaemon extends \Model\Model{

    use \Boot\Traits\Key;

    protected $fillable = ["attach_id", "daemon", "class_name"];

    static $key = ROOT."tmp/key/attach_daemon.daemon.key";

    public static function boot(){
        parent::boot();
        
        static::created(function($model){
            AttachDaemon::key_create();
        });
    }

    public function attach(){
        return $this->belongsTo(\General\Model\Attach::class);
    }
}