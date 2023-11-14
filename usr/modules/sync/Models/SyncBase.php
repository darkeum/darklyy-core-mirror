<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sync\Models;

use Boot\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SyncBase extends Model
{
    use SoftDeletes;

    /**
     * Доступные поля
     *
     * @var array
     */
    protected $fillable = ["uid", "title", "org", "type", "host", "user", "pass", "status"];
}
