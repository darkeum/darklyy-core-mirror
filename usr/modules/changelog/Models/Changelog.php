<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Changelog\Models;

use Boot\Models\Model;

class Changelog extends Model
{
    protected $fillable = ["uid", "category", "published_at", "title", "text", "informing", "important", "status"];

    public function getTextClearAttribute()
    {
        return ee(strip_tags($this->text));
    }

    public function getDateClearAttribute()
    {
        return date('d.m', strtotime($this->published_at));
    }

    public function getTypeColorAttribute()
    {
        return 'text-success';
    }

    public function read()
    {
        return $this->belongsTo(ChangelogRead::class, "id", 'changelog_id');
    }

}