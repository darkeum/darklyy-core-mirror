<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Models;

use Boot\Models\Model;

class ScadaValues extends Model
{
    protected $fillable = ["scada_tags_id", "value"];

    public function tags()
    {
        return $this->belongsTo(ScadaTags::class, "tags_id");
    }
}
