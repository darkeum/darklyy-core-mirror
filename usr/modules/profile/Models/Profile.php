<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Profile\Models;

use Boot\Models\Model;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\HasResourceKeyTrait;
use \Boot\System\Categories\Categories;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    //use HashIdTrait;
    //use HasResourceKeyTrait;
    //
    //use SoftDeletes;

    /**
     * Ключ ресурса, который будет использоваться в сериализованных ответах.
     *
     * @var string
     */
    protected $resourceKey = 'Profile';
    /**/

    protected $fillable = [
        "uid",
    ];

    protected $casts = [

    ];

    public static function boot(){

        parent::boot();
        
        static::creating(function($model){
        });

        static::created(function($model){
        });

        static::updating(function($model){
        });

    }
}