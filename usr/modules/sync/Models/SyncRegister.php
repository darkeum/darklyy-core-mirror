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

class SyncRegister extends Model
{
    /**
     * Доступные поля
     *
     * @var array
     */
    protected $fillable = ["uid", "module", "pid", "type", "date_add", "date_sync", "sync_base", "sync_id", "status"];
}
