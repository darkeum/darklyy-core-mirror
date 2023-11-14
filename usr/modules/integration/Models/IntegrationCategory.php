<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Integration\Models;

use Boot\Models\Model;
use Boot\Models\Categories;

class IntegrationCategory extends Model
{
    protected $fillable = ["uid", "category_id", "category_integration_id",  "category_integration_title", "type_integration"];

    public function categories()
    {
        return $this->belongsTo(Categories::class, "category_id");
    }
}
