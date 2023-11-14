<?php

namespace Catalog_v2\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class PumpLinks extends \Model\Model{
    use SoftDeletes;
    
    protected $fillable = ["cid", "link", "status"];
}