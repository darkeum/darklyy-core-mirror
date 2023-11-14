<?php

namespace Catalog_v2\Model;

use \Boot\Support\Facades\Auth;

class CatalogTypes extends \Model\Model{
    use \Boot\Traits\CacheAll;
    
    protected $fillable = ["uid", "title", "service", "ofd", "date", "status"];

    public function catalog(){
        return $this->hasMany(Catalog::class, "types");
    }
}