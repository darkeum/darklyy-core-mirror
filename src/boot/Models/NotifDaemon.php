<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

use Notifications\Models\Notifications;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotifDaemon extends Model
{
    use SoftDeletes;
    use \Boot\Traits\Key;

    protected $fillable = ["notif_id", "platform"];

    static $key = ROOT . "/tmp/key/notif.daemon.key";

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            static::key_create();
        });
    }

    public function notif()
    {
        return $this->belongsTo(Notifications::class, "notif_id");
    }
}
