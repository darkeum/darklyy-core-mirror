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
use Catalog\Models\CatalogAttribute;

class IntegrationAttribute extends Model
{
    protected $fillable = ["uid", "integration_category_id", "attribute_id", "attribute_integration_id", "attribute_integration_name", "attribute_integration_dictionary", "attribute_integration_type"];

    public function attribute()
    {
        return $this->belongsTo(CatalogAttribute::class, "attribute_id");
    }
}
