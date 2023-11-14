<?php

namespace Catalog\Model;

class CatalogManufactureStock extends \Boot\System\Database\Model\Model{
    protected $fillable = ["cid", "count", "date", "model_type"];
}