<?php

namespace Catalog_v2\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogSearch extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["title", "marka", "sku", "man", "cid", "mark"];
  
    // protected $casts = [
    //     'fill' => "type", //array, datetime, date
    // ];

}