<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Cashbox\UI\WEB\Controllers;

use Cashbox\Models\Cashbox;
use Cashbox\Models\CashierShifts;
use Boot\Controller\AbstractController;

class CashboxController extends AbstractController
{
    static function index()
    {
        return view('cashbox::manager.index');
    }

    static function show($id)
    {
        return view('cashbox::manager.show', [
            'id' => $id,
            'cashbox' => Cashbox::findOrFail($id),
            'cashier_shifts' => CashierShifts::with("cashier")->where("cashbox_id", $id)->where("status", 1)->first() ?? cc(),
        ]);
    }
}
