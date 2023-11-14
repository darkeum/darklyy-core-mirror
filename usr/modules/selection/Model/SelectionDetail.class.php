<?php

namespace Selection\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionDetail extends \Boot\System\Database\Model\Model{
    //use SoftDeletes;

    protected $fillable = ["selection_id", "cid", "count", "position", "status"];

}