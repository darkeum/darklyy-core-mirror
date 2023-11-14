<?php

namespace General\Model;

class Groups extends \Model\Model{
    protected $fillable = ["name", "guest", "user", "moderator", "admin", "banned", "showHide", "showAttach", "loadAttach", "addPost", "addComment", "allowRating", "maxWidth", "maxPms", "control", "icon", "color", "points", "protect", "special"];  
}