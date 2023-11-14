<?php

namespace Selection\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionArea extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["uid", "title", "object_id", "oid", "save_area", "status"];
  
    // protected $casts = [
    //     'fill' => "type", //array, datetime, date
    // ];

}