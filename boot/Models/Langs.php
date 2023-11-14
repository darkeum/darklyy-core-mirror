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

use Illuminate\Database\Eloquent\Relations\Relation;

class Langs extends Model
{
    protected $fillable = ["postId", "module", "title", "fulltitle", "short", "full", "lang"];
    protected $primaryKey = '_id';
    public $timestamps = false;
    protected $casts = [
        'time_marker' => "datetime",
        'agent' => "array",
    ];

    public function langeable()
    {
        return $this->morphTo(__FUNCTION__, 'module', 'postId');
    }

    public static function boot()
    {
        parent::boot();
        Relation::morphMap([
            'news' => \News\Models\News::class,

        ]);
    }
}
