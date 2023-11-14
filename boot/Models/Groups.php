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

class Groups extends Model
{
    protected $fillable = ["name", "guest", "user", "moderator", "admin", "banned", "showHide", "showAttach", "loadAttach", "addPost", "addComment", "allowRating", "maxWidth", "maxPms", "control", "icon", "color", "points", "protect", "special"];
}
