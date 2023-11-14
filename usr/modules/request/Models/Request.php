<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Request\Models;

use Boot\Models\Model;
use Boot\System\Database\Eloquent\SoftDeletes;

class Request extends Model{
    use SoftDeletes;
    protected $fillable = ["uid_dir", "uid_exec", "title", "title_user", "request", "request_data", "oid", "result", "object", "org", "type", "aid", "final_cid", "pid", "specification", "date", "status"];

    protected $dates = ['deleted_at'];
    
    protected $casts = [
      'request_data' => "array",
      'result' => "array",
    ];  
   
}