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

class ScadaCrash extends Model
{
    protected $fillable = ["scada_id", "tags_id", "type", "description"];

    public function scada()
    {
        return $this->belongsTo(Scada::class, "scada_id");
    }

    public function tags()
    {
        return $this->belongsTo(ScadaTags::class, "tags_id");
    }
}
