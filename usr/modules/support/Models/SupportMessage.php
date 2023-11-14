<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\Models;

use Boot\Models\Model;
use Boot\Models\Users;
use Boot\Models\Attach;
use Support\Models\Support;
use Boot\System\Media\HasMedia;
use Boot\System\Media\InteractsWithMedia;

class SupportMessage extends Model implements HasMedia
{
    
    use InteractsWithMedia;

    protected $fillable = ["support_id", "uid", "message", "media_id", "message_read"];

    protected $casts = [
        'message_read' => 'datetime',
    ];

    protected $appends = ["avatar", "full_name"];

    public function support()
    {
        return $this->belongsTo(Support::class);
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "uid");
    }

    public function attach()
    {
        return $this->belongsTo(Attach::class);
    }

    public function getAvatarAttribute()
    {
        return Users::avatar($this->uid);
    }

    public function getFullNameAttribute()
    {
        return fullUserName($this->uid);
    }
}
