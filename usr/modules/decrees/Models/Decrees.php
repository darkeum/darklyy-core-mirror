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

class Decrees extends Model
{
    use HashIdTrait;
    use HasResourceKeyTrait;
    use SoftDeletes;

    /**
     * Ключ ресурса, который будет использоваться в сериализованных ответах.
     *
     * @var string
     */
    protected $resourceKey = 'Decrees';

    protected $fillable = [
        "id",
        "numb",
        "numb_counter",
        "uid",
        "org",
        "comment",
        "started_at",
        "ended_at",
        "type",
        "file_id",
        "scan_id",
        "worker",
        "data",
        "status"
    ];

    protected $casts = [
        'started_at'  => "date",
        'ended_at'    => "date",
        'data'        => "array",
        'worker'      => "array",
    ];



}