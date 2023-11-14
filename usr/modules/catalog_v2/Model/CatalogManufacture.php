<?php

namespace Catalog_v2\Model;

class CatalogManufacture extends \Model\Model{
    use \Boot\Traits\CacheAll;

    protected $fillable = ["sync_id", "name", "preview", "sum"];
}