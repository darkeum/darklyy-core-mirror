<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Task\Models;

use Boot\Models\Model;

class Task extends Model
{
    protected $fillable = ["type", "title", "task", "fromid", "toid", "coperformerid", "observerid", "requestid", "object", "base", "date", "endsw", "date_end", "auto_delete", "status", "archive"];

    protected $appends = ["date_view", "date_end_view"];

    public function getDateViewAttribute()
    {
        return date("d.m.Y H:i:s", strtotime($this->date));
    }

    public function getDateEndViewAttribute()
    {
        return date("d.m.Y H:i:s", strtotime($this->date_end));
    }

    public function comments()
    {
        return $this->hasMany(\Task\Model\TaskComments::class, "tid");
    }
}
