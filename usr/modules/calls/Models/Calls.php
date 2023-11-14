<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

namespace Calls\Models;

use Boot\Models\Model;

class Calls extends Model
{
    protected $table = 'calls';
    protected $fillable = [
        'id',
        'src',
        'dst',
        'calldate',
        'duration',
        'billsec',
        'uniqueid', // Уникальное
        'recordingfile',

        'file',
        'type',
        'status'
    ];

    protected $dates = [
        'calldate_at',
    ];
}
