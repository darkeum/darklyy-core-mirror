<?php

namespace Catalog_v2\Model;

class CatalogSection extends \Boot\Models\Model{
    use \Boot\Traits\CacheAll;

    protected $fillable = ["title", "uid", "status"];

}