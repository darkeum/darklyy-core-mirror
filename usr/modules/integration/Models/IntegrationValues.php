<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Integration\Models;

use Boot\Models\Model;
use Catalog\Model\CatalogAttributeValue;

class IntegrationValues extends Model
{
    protected $fillable = ["uid", "integration_attribute_id", "value_id", "value_integration_id", "value_integration_value"];

    public function values()
    {
        return $this->belongsTo(CatalogAttributeValue::class, "value_id");
    }
}
