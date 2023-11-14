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
use Boot\System\Media\HasMedia;
use Support\Models\SupportMessage;
use Boot\System\Media\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Support extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = ["uid", "uid_dir", "type", "title", "place", "url", "mark", "view_at", "started_at", "planned_at", "completed_at", "status"];

    protected $appends = ["unread_messages"];

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "uid");
    }

    public function uid_dir()
    {
        return $this->belongsTo(Users::class, "uid_dir");
    }

    public function getUnreadMessagesAttribute()
    {
        return SupportMessage::where("support_id", $this->id)->count();
    }

    public function getFullNameAttribute()
    {
        return fullUserName($this->uid);
    }
}
