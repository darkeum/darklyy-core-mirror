<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Slider\Models;

use Boot\Models\Model;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\HasResourceKeyTrait;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
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
    protected $resourceKey = 'Slider';
    /**/

    protected $fillable = [
        'id',
        'title_top',
        'title_down',
        'desc',
        'img',
        'link',
        'bg_left',
        'bg_right',
        'priority',
        //'status',
    ];

    protected $casts = [
        //'preview'       => "array",
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //public static function boot(){
    //    //parent::boot();
    //}

}