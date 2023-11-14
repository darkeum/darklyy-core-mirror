<?php

namespace Project\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Scales extends \Boot\System\Database\Model\Model{
    use SoftDeletes;

    protected $fillable = ["types", "stand", "dia", "dlina", "material", "item", "status"];
  
    protected $casts = [
        'item' => "array", //array, datetime, date
    ];

}