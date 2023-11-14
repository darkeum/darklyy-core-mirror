<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Decrees\Models;

use Boot\Models\Model;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\HasResourceKeyTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Command extends Model
{
    use HashIdTrait;
    use HasResourceKeyTrait;
    use SoftDeletes;

    /**
     * Ключ ресурса, который будет использоваться в сериализованных ответах.
     *
     * @var string
     */

    protected $fillable = [
        "numb",
        "numb_counter",
        "uid",
        "org",
        "comment",
        "date",
        "date_end",
        "date_create",
        "type",
        "file",
        "scan",
        "worker",
        "data",
    ];

    protected $casts = [
        'date'              => "date",
        'date_end'          => "date",
        'date_create'       => "date",
        'data'              => "array",
        'worker'            => "array",
    ];



}