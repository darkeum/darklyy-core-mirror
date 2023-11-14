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
use Boot\Support\Facades\Auth;

class TaskComments extends Model
{
    protected $fillable = ["tid", "uid", "comment", "date", "cat", "status"];

    public function task()
    {
        return $this->belongsTo(\Task\Model\Task::class, "tid");
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date = empty($model->date) ? time() : $model->date;
        });
    }
}
