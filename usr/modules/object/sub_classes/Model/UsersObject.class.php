<?php

namespace Object\Model;

class UsersObject extends \Model\Model
{
    protected $fillable = ["uid", "object", "choose", "status"];

    protected $attributes = [
        'choose' => 0,
        'status' => 1,
    ];
}