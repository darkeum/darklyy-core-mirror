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
use Realization\Model\Realization;

class CashboxChecks extends Model
{
    /* TypeCheck 0 - проведение 1 - распроведение */
    protected $fillable = ["uid", "realization_id", "cashier_shifts_id", "data", "TypeCheck"];

    protected $casts = [
        'data' => 'object',
    ];

    public function realization()
    {
        return $this->belongsTo(Realization::class);
    }

    public function cashier_shifts()
    {
        return $this->belongsTo(CashierShifts::class, "cashier_shifts_id");
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "uid");
    }
}
