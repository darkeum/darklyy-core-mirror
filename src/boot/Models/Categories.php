<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

class Categories extends Model
{
    protected $fillable = ["id", "name", "altname", "fulltitle", "description", "full", "keywords", "module", "icon", "preview", "preview_og", "position", "position_filter", "parent_id", "sum", "hidden", "status"];

    public $timestamps = false;

    protected $casts = [
        'preview' => "array",
        'preview_og' => "array",
    ];
}
