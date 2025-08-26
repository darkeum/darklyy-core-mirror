<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

use Boot\Support\Facades\Categories as CategoriesFacade;

class Categories extends Model
{
    protected $fillable = ["id", "name", "altname", "fulltitle", "description", "full", "keywords", "code", "module", "icon", "preview", "preview_og", "position", "position_filter", "parent_id", "hidden", "status"];

    public $timestamps = false;

    protected $casts = [
        'preview'    => "array",
        'preview_og' => "array",
    ];
    public function getUrlAttribute()
    {
        return CategoriesFacade::getCategoryUrl($this->id);
    }
}
