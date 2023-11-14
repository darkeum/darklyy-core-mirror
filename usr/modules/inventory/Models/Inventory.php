<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Inventory\Models;

use Boot\Models\Model;
use Boot\Models\Users;
use Boot\Models\Categories;
use Organizations\Model\Organizations;
use Inventory\Models\InventoryLocation;

class Inventory extends Model
{
    protected $fillable = ["id", "wid", "serial_number", "category", "name", "spec", "org", "location", "uid", "date", "last_date", "preview", "status"];

    public function users()
    {
        return $this->belongsTo(Users::class, "wid");
    }  
    
    public function organizations()
    {
        return $this->belongsTo(Organizations::class, "org");
    } 
    
    public function categories()
    {
        return $this->belongsTo(Categories::class, "category");
    }
    
    public function locations()
    {
        return $this->belongsTo(InventoryLocation::class, "location");
    }
}
