<?php

namespace Users\Models;

use Boot\Models\Model;
use Objects\Models\Objects;

// use Objects\Models\Objects;

class UsersObject extends Model
{
    protected $fillable = [
        "uid",
        "object",
        "choose",
        "status"
    ];

    protected $attributes = [
        'choose' => 0,
        'status' => 1,
    ];

}