<?php

namespace Catalog_v2\Model;

class CatalogUnit extends \Model\Model{
    use \Boot\Traits\CacheAll;

    protected $fillable = ["unit", "name", "sname"];
}