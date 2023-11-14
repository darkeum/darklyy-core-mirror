<?php

namespace Catalog\Model;

class CatalogManufactureStockParser extends \Boot\System\Database\Model\Model{
    protected $fillable = ["model_type", "work", "last_date_file", "last_date"];
}