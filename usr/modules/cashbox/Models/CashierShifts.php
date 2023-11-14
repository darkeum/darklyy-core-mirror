<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Cashbox\Models;

use Boot\Models\Model;
use Boot\Models\Users;

class CashierShifts extends Model
{
    /* STATUS: 1 - смена открыта 2 - смена закрыта */
    protected $fillable = ["uid_start", "uid_end", "cashiers_id", "cashbox_id", "shift_number", "start_shift", "end_shift", "number_checks", "data_start", "data_end", "status"];

    protected $casts = [
        'data_start' => 'object',
        'data_end' => 'object',
    ];

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function cashier()
    {
        return $this->belongsTo(Cashiers::class, "cashiers_id");
    }

    public function user_start()
    {
        return $this->belongsTo(Users::class, "uid_start");
    }

    public function user_end()
    {
        return $this->belongsTo(Users::class, "uid_end");
    }
}
