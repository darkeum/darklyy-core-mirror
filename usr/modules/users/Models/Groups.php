<?php

// Скопирован из Boot - НЕ АКТУАЛЕН ИСПОЛЬЗУЕТСЯ Roles

namespace Users\Models;

use Boot\Models\Model;

class Groups extends Model
{
    protected $fillable = ["name", "guest", "user", "moderator", "admin", "banned", "showHide", "showAttach", "loadAttach", "addPost", "addComment", "allowRating", "maxWidth", "maxPms", "control", "icon", "color", "points", "protect", "special"];
}
