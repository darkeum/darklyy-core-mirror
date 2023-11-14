<?php

namespace Objects\Models;

use Boot\Models\Model;
use Sales\Models\Sales;
use Task\Model\Task;

class Objects extends Model
{
    protected $table = "object";

    protected $fillable = [
        "id",
        "name",
        "address",
        "coords",
        "comment",

        "cat",
        "date",

        "uid",
        "preview",

        "id_client",
        "id_con",
        "id_zas",
        "id_pod",
        "id_gpr",
        "id_spr",

        "status",
        "status_action"
    ];

    public function sales()
    {
        return $this->hasMany(Sales::class, 'object');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'object');
    }

    public function users(){
        return $this->hasMany(\Users\Models\UsersObject::class, "object", 'id');
    }

}