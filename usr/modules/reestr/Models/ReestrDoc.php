<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Reestr\Models;

use Boot\Models\Model;
use Boot\Models\Attach;

class ReestrDoc extends Model
{
    protected $fillable = ["reestr_id", "type", "date_create", "date", "comment", "uid_took", "title", "doc", "scan", "spec_numb", "sign", "status"];

    public function scanFile()
    {
        return $this->belongsTo(Attach::class, 'scan');
    }

    public function docFile()
    {
        return $this->belongsTo(Attach::class, 'doc');
    }
}
